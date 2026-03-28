<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dosen;
use App\Models\JadwalMingguan;
use App\Models\JadwalAkanDatang;
use App\Models\JadwalDadakan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard Dosen (auth required)
     */
    public function index()
    {
        $user = Auth::user();
        $dosen = Dosen::where('email', $user->email)->first();
        $hariIni = now()->locale('id')->isoFormat('dddd');

        if (!$dosen) return view('dashboard.dosen', compact('user', 'dosen', 'hariIni'))->with('error', 'Data dosen tidak ditemukan');

        $jadwalMingguan = $dosen->jadwalMingguan()
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 END")
            ->orderBy('jam_mulai')->get();

        $jadwalAkanDatang = $dosen->jadwalAkanDatang()
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->orderBy('tanggal_mulai')->get();

        $jadwalDadakan = $dosen->jadwalDadakan()
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->orderBy('tanggal_mulai')->get();

        return view('dashboard.dosen', compact('user', 'dosen', 'hariIni', 'jadwalMingguan', 'jadwalAkanDatang', 'jadwalDadakan'));
    }

    /**
     * Update status manual dosen
     */
    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        $dosen = Dosen::where('email', $user->email)->first();
        if (!$dosen) return back()->with('error', 'Data dosen tidak ditemukan');

        // Update mode (otomatis/manual)
        if ($request->has('update_mode')) {
            $dosen->update(['status_mode' => $request->status_mode]);

            // Jika switch ke otomatis, langsung sync dari jadwal
            if ($request->status_mode === 'otomatis') {
                $dosen->syncStatusFromJadwal();
            }

            return back()->with('success', 'Mode status diperbarui ke ' . ucfirst($request->status_mode));
        }

        // Update status manual
        $dosen->update(['status' => $request->status]);
        return back()->with('success', 'Status berhasil diperbarui');
    }

    /**
     * Update profil dosen (nomor telepon)
     */
    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $dosen = Dosen::where('email', $user->email)->first();
        if (!$dosen) return back()->with('error', 'Data dosen tidak ditemukan');

        $request->validate([
            'telepon' => 'nullable|string|max:20',
        ]);

        $dosen->update(['telepon' => $request->telepon]);
        return back()->with('success_profil', 'Nomor WhatsApp berhasil disimpan');
    }

    /**
     * Halaman publik untuk mahasiswa (TANPA LOGIN)
     */
    public function publicPage(Request $request)
    {
        $hariIni = now()->locale('id')->isoFormat('dddd');
        $today = now()->toDateString();

        // Sync status semua dosen berdasarkan jadwal aktif
        foreach (Dosen::all() as $d) {
            $d->syncStatusFromJadwal();
        }

        $dosenList = Dosen::with([
            'jadwalMingguan' => fn($q) => $q->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 END")->orderBy('jam_mulai'),
            'jadwalAkanDatang' => fn($q) => $q->where('tanggal_selesai', '>=', $today)->orderBy('tanggal_mulai'),
            'jadwalDadakan' => fn($q) => $q->where('tanggal_selesai', '>=', $today)->orderBy('tanggal_mulai'),
        ])->get();

        // Fitur cari per tanggal
        $cariTanggal = $request->get('tanggal');

        return view('public.status', compact('dosenList', 'hariIni', 'cariTanggal'));
    }

    /**
     * API JSON untuk auto-refresh
     */
    public function apiStatus()
    {
        foreach (Dosen::all() as $d) {
            $d->syncStatusFromJadwal();
        }

        $data = Dosen::all()->map(fn($d) => [
            'nama' => $d->nama,
            'nidn' => $d->nidn,
            'jabatan' => $d->jabatan,
            'status' => $d->status,
        ]);

        return response()->json($data);
    }
}
