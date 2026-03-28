<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dosen;
use App\Models\JadwalMingguan;
use App\Models\JadwalAkanDatang;
use App\Models\JadwalDadakan;

class JadwalController extends Controller
{
    private function getDosen()
    {
        return Dosen::where('email', Auth::user()->email)->firstOrFail();
    }

    private function syncAfterChange(Dosen $dosen): void
    {
        $dosen->syncStatusFromJadwal();
    }

    // ===================== JADWAL MINGGUAN =====================

    public function storeMingguan(Request $request)
    {
        $request->validate([
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kegiatan' => 'required',
        ]);

        $dosen = $this->getDosen();
        JadwalMingguan::create(array_merge($request->only(['hari', 'jam_mulai', 'jam_selesai', 'kegiatan', 'mata_kuliah', 'ruangan', 'keterangan']), ['dosen_id' => $dosen->id]));

        $this->syncAfterChange($dosen);
        return back()->with('success', 'Jadwal mingguan berhasil ditambahkan');
    }

    public function updateMingguan(Request $request, int $id)
    {
        $request->validate([
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kegiatan' => 'required',
        ]);

        $dosen = $this->getDosen();
        $jadwal = JadwalMingguan::where('id', $id)->where('dosen_id', $dosen->id)->firstOrFail();
        $jadwal->update($request->only(['hari', 'jam_mulai', 'jam_selesai', 'kegiatan', 'mata_kuliah', 'ruangan', 'keterangan']));

        $this->syncAfterChange($dosen);
        return back()->with('success', 'Jadwal mingguan berhasil diperbarui');
    }

    public function destroyMingguan(int $id)
    {
        $dosen = $this->getDosen();
        JadwalMingguan::where('id', $id)->where('dosen_id', $dosen->id)->firstOrFail()->delete();

        $this->syncAfterChange($dosen);
        return back()->with('success', 'Jadwal mingguan berhasil dihapus');
    }

    // ===================== JADWAL AKAN DATANG =====================

    public function storeAkanDatang(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $dosen = $this->getDosen();
        $data = $request->only(['judul', 'tanggal_mulai', 'tanggal_selesai', 'keterangan']);
        $data['dosen_id'] = $dosen->id;
        $data['is_fullday'] = $request->boolean('is_fullday', true);
        if (!$data['is_fullday']) {
            $data['jam_mulai'] = $request->jam_mulai;
            $data['jam_selesai'] = $request->jam_selesai;
        }

        JadwalAkanDatang::create($data);

        $this->syncAfterChange($dosen);
        return back()->with('success', 'Jadwal akan datang berhasil ditambahkan');
    }

    public function updateAkanDatang(Request $request, int $id)
    {
        $request->validate([
            'judul' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $dosen = $this->getDosen();
        $jadwal = JadwalAkanDatang::where('id', $id)->where('dosen_id', $dosen->id)->firstOrFail();
        $data = $request->only(['judul', 'tanggal_mulai', 'tanggal_selesai', 'keterangan']);
        $data['is_fullday'] = $request->boolean('is_fullday', true);
        if (!$data['is_fullday']) {
            $data['jam_mulai'] = $request->jam_mulai;
            $data['jam_selesai'] = $request->jam_selesai;
        } else {
            $data['jam_mulai'] = null;
            $data['jam_selesai'] = null;
        }
        $jadwal->update($data);

        $this->syncAfterChange($dosen);
        return back()->with('success', 'Jadwal akan datang berhasil diperbarui');
    }

    public function destroyAkanDatang(int $id)
    {
        $dosen = $this->getDosen();
        JadwalAkanDatang::where('id', $id)->where('dosen_id', $dosen->id)->firstOrFail()->delete();

        $this->syncAfterChange($dosen);
        return back()->with('success', 'Jadwal akan datang berhasil dihapus');
    }

    // ===================== JADWAL DADAKAN =====================

    public function storeDadakan(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'jam_selesai' => 'required_if:is_fullday,0',
        ]);

        $dosen = $this->getDosen();
        $data = $request->only(['judul', 'keterangan']);
        $data['dosen_id'] = $dosen->id;
        $data['tanggal_mulai'] = now()->format('Y-m-d');
        $data['tanggal_selesai'] = now()->format('Y-m-d');
        $data['is_fullday'] = $request->boolean('is_fullday', true);
        if (!$data['is_fullday']) {
            $data['jam_mulai'] = now()->format('H:i:s');
            $data['jam_selesai'] = $request->jam_selesai;
        }

        JadwalDadakan::create($data);

        $this->syncAfterChange($dosen);
        return back()->with('success', 'Jadwal dadakan berhasil ditambahkan');
    }

    public function updateDadakan(Request $request, int $id)
    {
        $request->validate([
            'judul' => 'required|string',
            'jam_selesai' => 'required_if:is_fullday,0',
        ]);

        $dosen = $this->getDosen();
        $jadwal = JadwalDadakan::where('id', $id)->where('dosen_id', $dosen->id)->firstOrFail();
        $data = $request->only(['judul', 'keterangan']);
        $data['is_fullday'] = $request->boolean('is_fullday', true);
        if (!$data['is_fullday']) {
            $data['jam_mulai'] = $jadwal->jam_mulai ?: now()->format('H:i:s');
            $data['jam_selesai'] = $request->jam_selesai;
        } else {
            $data['jam_mulai'] = null;
            $data['jam_selesai'] = null;
        }
        $jadwal->update($data);

        $this->syncAfterChange($dosen);
        return back()->with('success', 'Jadwal dadakan berhasil diperbarui');
    }

    public function destroyDadakan(int $id)
    {
        $dosen = $this->getDosen();
        JadwalDadakan::where('id', $id)->where('dosen_id', $dosen->id)->firstOrFail()->delete();

        $this->syncAfterChange($dosen);
        return back()->with('success', 'Jadwal dadakan berhasil dihapus');
    }
}
