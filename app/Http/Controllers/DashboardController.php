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
        $dosen = Dosen::fromAuth();
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

        $riwayatAkanDatang = $dosen->jadwalAkanDatang()
            ->where('tanggal_selesai', '<', $today)
            ->orderByDesc('tanggal_selesai')->get();

        $riwayatDadakan = $dosen->jadwalDadakan()
            ->where(function($q) use ($today, $nowTime) {
                $q->where('tanggal_selesai', '<', $today)
                  ->orWhere(function($q2) use ($today, $nowTime) {
                      $q2->where('tanggal_selesai', '=', $today)
                         ->where('is_fullday', false)
                         ->where('jam_selesai', '<', $nowTime);
                  });
            })
            ->orderByDesc('tanggal_selesai')->get();

        return view('dashboard.dosen', compact('user', 'dosen', 'hariIni', 'jadwalMingguan', 'jadwalAkanDatang', 'jadwalDadakan', 'riwayatAkanDatang', 'riwayatDadakan'));
    }

    /**
     * Update status manual dosen
     */
    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        $dosen = Dosen::fromAuth();
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
     * Update profil lengkap dosen
     */
    public function updateProfil(Request $request)
    {
        $user  = Auth::user();
        $dosen = Dosen::fromAuth();
        if (!$dosen) return back()->with('error', 'Data dosen tidak ditemukan');

        $request->validate([
            'nama'           => 'required|string|max:100',
            'nidn'           => 'nullable|string|max:20',
            'nik'            => 'nullable|string|max:20',
            'jabatan'        => 'nullable|string|max:100',
            'ruangan'        => 'nullable|string|max:100',
            'telepon'        => 'nullable|string|max:20',
            'tampilkan_nidn' => 'nullable|boolean',
            'tampilkan_nik'  => 'nullable|boolean',
            'email'          => 'required|email|max:100',
            'password'       => 'nullable|string|min:6|confirmed',
        ]);

        // Update data dosen
        $dosen->update([
            'nama'           => $request->nama,
            'nidn'           => $request->nidn,
            'nik'            => $request->nik,
            'jabatan'        => $request->jabatan,
            'ruangan'        => $request->ruangan,
            'telepon'        => $request->telepon,
            'tampilkan_nidn' => $request->boolean('tampilkan_nidn'),
            'tampilkan_nik'  => $request->boolean('tampilkan_nik'),
            'email'          => $request->email,
        ]);

        // Update email pada tabel users
        if ($user && $user->email !== $request->email) {
            $user->update(['email' => $request->email]);
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        return back()->with('success_profil', 'Profil berhasil diperbarui');
    }

    /**
     * Halaman publik untuk mahasiswa (TANPA LOGIN)
     */
    public function publicPage(Request $request)
    {
        $hariIni = now()->locale('id')->isoFormat('dddd');
        $today   = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        // [SEBELUMNYA ADA AUTO-DELETE DI SINI, SEKARANG DIHAPUS KARENA BUTUH FITUR RIWAYAT]
        foreach (\App\Models\Dosen::all() as $d) {
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
            // Hapus auto-delete untuk menjaga fitur Riwayat

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
