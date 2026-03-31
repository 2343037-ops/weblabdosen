<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dosen;
use App\Models\JadwalMingguan;
use App\Models\JadwalAkanDatang;
use App\Models\JadwalDadakan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private function syncDosenStatus(Dosen $dosen)
    {
        $dosen->syncStatusFromJadwal();
    }

    /**
     * Dashboard Admin: List semua dosen
     */
    public function index()
    {
        $dosenList = Dosen::with('user')->get();
        return view('admin.dashboard', compact('dosenList'));
    }

    /**
     * Tambah Dosen Baru
     */
    public function storeDosen(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'jabatan' => 'nullable|string|max:100',
            // fields lainnya optional
        ]);

        DB::transaction(function () use ($request) {
            // Create User
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'dosen',
            ]);

            // Create Dosen
            Dosen::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'nama' => $request->nama,
                'email' => $request->email,
                'jabatan' => $request->jabatan,
                'nidn' => $request->nidn,
                'nik' => $request->nik,
                'telepon' => $request->telepon,
                'ruangan' => $request->ruangan,
                'tampilkan_nidn' => $request->boolean('tampilkan_nidn'),
                'tampilkan_nik' => $request->boolean('tampilkan_nik'),
                'status' => 'Tidak Di Ruangan',
                'status_mode' => 'otomatis'
            ]);
        });

        return back()->with('success', 'Dosen berhasil ditambahkan.');
    }

    /**
     * Update Dosen (Profil & Password)
     */
    public function updateDosen(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . ($dosen->user_id ?? 'NULL'),
            'jabatan' => 'nullable|string|max:100',
        ]);

        $dosen->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'nidn' => $request->nidn,
            'nik' => $request->nik,
            'telepon' => $request->telepon,
            'ruangan' => $request->ruangan,
            'tampilkan_nidn' => $request->boolean('tampilkan_nidn'),
            'tampilkan_nik' => $request->boolean('tampilkan_nik'),
        ]);

        if ($dosen->user) {
            $userUpdate = ['name' => $request->nama, 'email' => $request->email];
            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }
            $dosen->user->update($userUpdate);
        }

        return back()->with('success', 'Data dosen berhasil diperbarui.');
    }

    /**
     * Hapus Dosen
     */
    public function destroyDosen($id)
    {
        $dosen = Dosen::findOrFail($id);
        $user = $dosen->user;
        
        $dosen->delete(); // jadwal will be cascaded
        if ($user) $user->delete();

        return back()->with('success', 'Dosen berhasil dihapus.');
    }

    // ===================== KELOLA JADWAL OLEH ADMIN =====================

    public function manageJadwal($id)
    {
        $dosen = Dosen::findOrFail($id);
        $hariIni = now()->locale('id')->isoFormat('dddd');
        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $jadwalMingguan = $dosen->jadwalMingguan()
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 END")
            ->orderBy('jam_mulai')->get();

        $jadwalAkanDatang = $dosen->jadwalAkanDatang()
            ->where('tanggal_selesai', '>=', $today)
            ->orderBy('tanggal_mulai')->get();

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

        return view('admin.manage-jadwal', compact('dosen', 'hariIni', 'jadwalMingguan', 'jadwalAkanDatang', 'jadwalDadakan', 'riwayatAkanDatang', 'riwayatDadakan'));
    }

    // --- Jadwal Mingguan ---
    public function storeMingguan(Request $request, $id)
    {
        $request->validate(['hari'=>'required', 'jam_mulai'=>'required', 'jam_selesai'=>'required', 'kegiatan'=>'required']);
        $dosen = Dosen::findOrFail($id);
        JadwalMingguan::create(array_merge($request->only(['hari','jam_mulai','jam_selesai','kegiatan','mata_kuliah','ruangan','keterangan']), ['dosen_id' => $dosen->id]));
        $this->syncDosenStatus($dosen);
        return back()->with('success', 'Jadwal mingguan disimpan.');
    }

    public function updateMingguan(Request $request, $id, $jadwalId)
    {
        $request->validate(['hari'=>'required', 'jam_mulai'=>'required', 'jam_selesai'=>'required', 'kegiatan'=>'required']);
        $dosen = Dosen::findOrFail($id);
        JadwalMingguan::where('id', $jadwalId)->where('dosen_id', $dosen->id)->firstOrFail()
            ->update($request->only(['hari','jam_mulai','jam_selesai','kegiatan','mata_kuliah','ruangan','keterangan']));
        $this->syncDosenStatus($dosen);
        return back()->with('success', 'Jadwal mingguan diperbarui.');
    }

    public function destroyMingguan($id, $jadwalId)
    {
        $dosen = Dosen::findOrFail($id);
        JadwalMingguan::where('id', $jadwalId)->where('dosen_id', $dosen->id)->firstOrFail()->delete();
        $this->syncDosenStatus($dosen);
        return back()->with('success', 'Jadwal dihapus.');
    }

    // --- Jadwal Akan Datang ---
    public function storeAkanDatang(Request $request, $id)
    {
        $request->validate(['judul'=>'required','tanggal_mulai'=>'required|date','tanggal_selesai'=>'required|date|after_or_equal:tanggal_mulai']);
        $dosen = Dosen::findOrFail($id);
        $data = $request->only(['judul','tanggal_mulai','tanggal_selesai','keterangan']);
        $data['dosen_id'] = $dosen->id;
        $data['is_fullday'] = $request->boolean('is_fullday', true);
        if (!$data['is_fullday']) { $data['jam_mulai']=$request->jam_mulai; $data['jam_selesai']=$request->jam_selesai; }
        JadwalAkanDatang::create($data);
        $this->syncDosenStatus($dosen);
        return back()->with('success', 'Jadwal diperbarui.');
    }

    public function updateAkanDatang(Request $request, $id, $jadwalId)
    {
        $request->validate(['judul'=>'required','tanggal_mulai'=>'required|date','tanggal_selesai'=>'required|date|after_or_equal:tanggal_mulai']);
        $dosen = Dosen::findOrFail($id);
        $data = $request->only(['judul','tanggal_mulai','tanggal_selesai','keterangan']);
        $data['is_fullday'] = $request->boolean('is_fullday', true);
        if (!$data['is_fullday']) { $data['jam_mulai']=$request->jam_mulai; $data['jam_selesai']=$request->jam_selesai; }
        else { $data['jam_mulai']=null; $data['jam_selesai']=null; }
        JadwalAkanDatang::where('id', $jadwalId)->where('dosen_id', $dosen->id)->firstOrFail()->update($data);
        $this->syncDosenStatus($dosen);
        return back()->with('success', 'Jadwal akan datang diperbarui.');
    }

    public function destroyAkanDatang($id, $jadwalId)
    {
        $dosen = Dosen::findOrFail($id);
        JadwalAkanDatang::where('id', $jadwalId)->where('dosen_id', $dosen->id)->firstOrFail()->delete();
        $this->syncDosenStatus($dosen);
        return back()->with('success', 'Jadwal dihapus.');
    }

    // --- Jadwal Dadakan ---
    public function storeDadakan(Request $request, $id)
    {
        $request->validate(['judul'=>'required','jam_selesai'=>'required_if:is_fullday,0']);
        $dosen = Dosen::findOrFail($id);
        $data = $request->only(['judul','keterangan']);
        $data['dosen_id'] = $dosen->id;
        $data['tanggal_mulai'] = now()->format('Y-m-d'); $data['tanggal_selesai'] = now()->format('Y-m-d');
        $data['is_fullday'] = $request->boolean('is_fullday', true);
        if (!$data['is_fullday']) { $data['jam_mulai']=now()->format('H:i:s'); $data['jam_selesai']=$request->jam_selesai; }
        JadwalDadakan::create($data);
        $this->syncDosenStatus($dosen);
        return back()->with('success', 'Jadwal diperbarui.');
    }

    public function updateDadakan(Request $request, $id, $jadwalId)
    {
        $request->validate(['judul'=>'required','jam_selesai'=>'required_if:is_fullday,0']);
        $dosen = Dosen::findOrFail($id);
        $jadwal = JadwalDadakan::where('id', $jadwalId)->where('dosen_id', $dosen->id)->firstOrFail();
        $data = $request->only(['judul','keterangan']);
        $data['is_fullday'] = $request->boolean('is_fullday', true);
        if (!$data['is_fullday']) { $data['jam_mulai']=$jadwal->jam_mulai?:now()->format('H:i:s'); $data['jam_selesai']=$request->jam_selesai; }
        else { $data['jam_mulai']=null; $data['jam_selesai']=null; }
        $jadwal->update($data);
        $this->syncDosenStatus($dosen);
        return back()->with('success', 'Jadwal dadakan diperbarui.');
    }

    public function destroyDadakan($id, $jadwalId)
    {
        $dosen = Dosen::findOrFail($id);
        JadwalDadakan::where('id', $jadwalId)->where('dosen_id', $dosen->id)->firstOrFail()->delete();
        $this->syncDosenStatus($dosen);
        return back()->with('success', 'Jadwal dihapus.');
    }
}
