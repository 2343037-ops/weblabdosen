<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Dosen extends Model
{
    protected $table = 'dosen';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'user_id', 'nama', 'nidn', 'nik', 'jabatan', 'email', 'telepon', 'ruangan', 'tampilkan_nidn', 'tampilkan_nik', 'status', 'status_mode'];

    protected $casts = [
        'tampilkan_nidn' => 'boolean',
        'tampilkan_nik'  => 'boolean',
    ];

    // ─── Relasi ke User (akun login) ───────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Helper: ambil dosen dari user yang sedang login ───
    public static function fromAuth(): ?self
    {
        $user = Auth::user();
        if (!$user) return null;

        // Prioritas: cari lewat user_id (FK eksplisit)
        if ($user->dosen) return $user->dosen;

        // Fallback: cari lewat email (untuk data lama sebelum migrasi)
        return self::where('email', $user->email)->first();
    }

    // ─── Relasi ke jadwal ────────────────────────────────────
    public function jadwalMingguan()
    {
        return $this->hasMany(JadwalMingguan::class, 'dosen_id');
    }

    public function jadwalAkanDatang()
    {
        return $this->hasMany(JadwalAkanDatang::class, 'dosen_id');
    }

    public function jadwalDadakan()
    {
        return $this->hasMany(JadwalDadakan::class, 'dosen_id');
    }

    /**
     * Sinkronisasi status otomatis berdasarkan jadwal dan jam kerja.
     *
     * Logika:
     * - Jika mode MANUAL → tidak diubah otomatis
     * - Jika mode OTOMATIS:
     *   1. Di luar jam kerja (sebelum 08:00 atau setelah 17:00) → Tidak Di Ruangan
     *   2. Ada jadwal dadakan aktif → Tidak Di Ruangan
     *   3. Ada jadwal akan datang aktif → Tidak Di Ruangan
     *   4. Ada jadwal mingguan aktif (mengajar/rapat/dll) → Tidak Di Ruangan
     *   5. Dalam jam kerja tanpa jadwal aktif → Di Ruangan
     */
    public function syncStatusFromJadwal(): void
    {
        // Jika mode manual, jangan ubah status otomatis
        if ($this->status_mode === 'manual') {
            return;
        }

        $now = Carbon::now();
        $hariMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        $hari = $hariMap[$now->format('l')] ?? $now->format('l');
        $jam = $now->format('H:i:s');
        $today = $now->toDateString();

        // Jam kerja: 08:00 - 17:00
        $jamMasuk = '08:00:00';
        $jamPulang = '17:00:00';

        // 1. Di luar jam kerja → Tidak Di Ruangan
        if ($jam < $jamMasuk || $jam >= $jamPulang) {
            $this->update(['status' => 'Tidak Di Ruangan']);
            return;
        }

        // 2. Hari Sabtu & Minggu → Tidak Di Ruangan
        if ($hari === 'Sabtu' || $hari === 'Minggu') {
            $this->update(['status' => 'Tidak Di Ruangan']);
            return;
        }

        // 3. Cek jadwal dadakan fullday hari ini
        $dadakanFullday = $this->jadwalDadakan()
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->where('is_fullday', true)
            ->exists();

        if ($dadakanFullday) {
            $this->update(['status' => 'Tidak Di Ruangan']);
            return;
        }

        // 4. Cek jadwal dadakan dengan jam spesifik
        $dadakanJam = $this->jadwalDadakan()
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->where('is_fullday', false)
            ->where('jam_mulai', '<=', $jam)
            ->where('jam_selesai', '>=', $jam)
            ->exists();

        if ($dadakanJam) {
            $this->update(['status' => 'Tidak Di Ruangan']);
            return;
        }

        // 5. Cek jadwal akan datang fullday hari ini
        $akanDatangFullday = $this->jadwalAkanDatang()
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->where('is_fullday', true)
            ->exists();

        if ($akanDatangFullday) {
            $this->update(['status' => 'Tidak Di Ruangan']);
            return;
        }

        // 6. Cek jadwal akan datang dengan jam
        $akanDatangJam = $this->jadwalAkanDatang()
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->where('is_fullday', false)
            ->where('jam_mulai', '<=', $jam)
            ->where('jam_selesai', '>=', $jam)
            ->exists();

        if ($akanDatangJam) {
            $this->update(['status' => 'Tidak Di Ruangan']);
            return;
        }

        // 7. Cek jadwal mingguan (mengajar, rapat, dll)
        $mingguanAktif = $this->jadwalMingguan()
            ->where('hari', $hari)
            ->where('jam_mulai', '<=', $jam)
            ->where('jam_selesai', '>=', $jam)
            ->exists();

        if ($mingguanAktif) {
            $this->update(['status' => 'Tidak Di Ruangan']);
            return;
        }

        // 8. Dalam jam kerja, tidak ada jadwal aktif → Di Ruangan
        $this->update(['status' => 'Di Ruangan']);
    }
}
