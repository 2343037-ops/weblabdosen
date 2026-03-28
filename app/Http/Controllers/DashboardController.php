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

        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $jadwalMingguan = $dosen->jadwalMingguan()
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 END")
            ->orderBy('jam_mulai')->get();

        // Jadwal Akan Datang: yang belum selesai (tanggal_selesai >= hari ini)
        $jadwalAkanDatang = $dosen->jadwalAkanDatang()
            ->where('tanggal_selesai', '>=', $today)
            ->orderBy('tanggal_mulai')->get();

        // Jadwal Dadakan: filter komprehensif
        // - Fullday: tampil selama tanggal_selesai >= hari ini
        // - Non-fullday: tampil selama jam_selesai >= sekarang (hari ini) ATAU tanggal_selesai > hari ini
        $jadwalDadakan = $dosen->jadwalDadakan()
            ->where(function($q) use ($today, $nowTime) {
                $q->where('tanggal_selesai', '>', $today)
                  ->orWhere(function($q2) use ($today, $nowTime) {
                      $q2->where('tanggal_selesai', '=', $today)
                         ->where(function($q3) use ($nowTime) {
                             $q3->where('is_fullday', true)
                                ->orWhere('jam_selesai', '>=', $nowTime);
                         });
                  });
            })
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
        $today   = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        // Auto-cleanup: hapus jadwal dadakan yang sudah benar-benar kadaluarsa
        // - Fullday: tanggal_selesai < hari ini
        // - Non-fullday: tanggal_selesai < hari ini ATAU (tanggal_selesai = hari ini DAN jam_selesai < sekarang)
        foreach (Dosen::all() as $d) {
            $d->jadwalDadakan()
              ->where(function($q) use ($today, $nowTime) {
                  $q->where('tanggal_selesai', '<', $today)
                    ->orWhere(function($q2) use ($today, $nowTime) {
                        $q2->where('tanggal_selesai', '=', $today)
                           ->where('is_fullday', false)
                           ->where('jam_selesai', '<', $nowTime);
                    });
              })
              ->delete();

            // Sync status setelah cleanup
            $d->syncStatusFromJadwal();
        }

        $dosenList = Dosen::with([
            'jadwalMingguan' => fn($q) => $q
                ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 END")
                ->orderBy('jam_mulai'),

            // Jadwal Akan Datang: seluruh event yang belum selesai
            'jadwalAkanDatang' => fn($q) => $q
                ->where('tanggal_selesai', '>=', $today)
                ->orderBy('tanggal_mulai'),

            // Jadwal Dadakan: filter komprehensif per tanggal + jam
            'jadwalDadakan' => fn($q) => $q
                ->where(function($q2) use ($today, $nowTime) {
                    $q2->where('tanggal_selesai', '>', $today)
                       ->orWhere(function($q3) use ($today, $nowTime) {
                           $q3->where('tanggal_selesai', '=', $today)
                              ->where(function($q4) use ($nowTime) {
                                  $q4->where('is_fullday', true)
                                     ->orWhere('jam_selesai', '>=', $nowTime);
                              });
                       });
                })
                ->orderBy('tanggal_mulai'),
        ])->get();

        $cariTanggal = $request->get('tanggal');

        return view('public.status', compact('dosenList', 'hariIni', 'cariTanggal'));
    }

    /**
     * API JSON untuk auto-refresh
     */
    public function apiStatus()
    {
        $today   = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        foreach (Dosen::all() as $d) {
            // Hapus dadakan kadaluarsa sebelum sync
            $d->jadwalDadakan()
              ->where(function($q) use ($today, $nowTime) {
                  $q->where('tanggal_selesai', '<', $today)
                    ->orWhere(function($q2) use ($today, $nowTime) {
                        $q2->where('tanggal_selesai', '=', $today)
                           ->where('is_fullday', false)
                           ->where('jam_selesai', '<', $nowTime);
                    });
              })
              ->delete();

            $d->syncStatusFromJadwal();
        }

        $data = Dosen::all()->map(fn($d) => [
            'nama'    => $d->nama,
            'nidn'    => $d->nidn,
            'jabatan' => $d->jabatan,
            'status'  => $d->status,
        ]);

        return response()->json($data);
    }
}
