<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Dosen;
use App\Models\JadwalMingguan;
use App\Models\JadwalAkanDatang;
use App\Models\JadwalDadakan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // USERS (Dosen Login)
        // ============================================================
        User::create(['name' => 'Ivan Haristyawan', 'email' => 'ivan.haristyawan@wicida.ac.id', 'password' => Hash::make('password'), 'role' => 'dosen']);
        User::create(['name' => 'Ahmad Fajri', 'email' => 'ahmad.fajri@wicida.ac.id', 'password' => Hash::make('password'), 'role' => 'dosen']);
        User::create(['name' => 'Rizki Galang Rahmadani', 'email' => 'rizki.galang@wicida.ac.id', 'password' => Hash::make('password'), 'role' => 'dosen']);

        // ============================================================
        // DOSEN
        // ============================================================
        Dosen::create(['id' => 'DSN001', 'nama' => 'Ivan Haristyawan, S.T., M.M.', 'nidn' => '1108117701', 'jabatan' => 'Kepala Lab', 'email' => 'ivan.haristyawan@wicida.ac.id']);
        Dosen::create(['id' => 'DSN002', 'nama' => 'Ahmad Fajri, S.Kom., M.Kom', 'nidn' => '1116088202', 'jabatan' => 'Staf Lab', 'email' => 'ahmad.fajri@wicida.ac.id']);
        Dosen::create(['id' => 'DSN003', 'nama' => 'Rizki Galang Rahmadani, S.Kom., M.Kom', 'nidn' => '1116088201', 'jabatan' => 'Staf Lab', 'email' => 'rizki.galang@wicida.ac.id']);

        // ============================================================
        // JADWAL MINGGUAN
        // ============================================================
        // DSN001 - Ivan Haristyawan
        JadwalMingguan::create(['dosen_id' => 'DSN001', 'hari' => 'Senin', 'jam_mulai' => '13:00', 'jam_selesai' => '14:40', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Algoritma dan Pemrograman 2 (TI/II/2/PB)', 'ruangan' => 'Ruang 3/5']);
        JadwalMingguan::create(['dosen_id' => 'DSN001', 'hari' => 'Rabu', 'jam_mulai' => '19:00', 'jam_selesai' => '20:30', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Algoritma dan Pemrograman 2 (TI/II/2/M)', 'ruangan' => 'Ruang 3/5']);
        JadwalMingguan::create(['dosen_id' => 'DSN001', 'hari' => 'Jumat', 'jam_mulai' => '13:00', 'jam_selesai' => '14:40', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Sistem Multimedia (TI/IV/2/PA)', 'ruangan' => 'Ruang 3/5']);
        JadwalMingguan::create(['dosen_id' => 'DSN001', 'hari' => 'Jumat', 'jam_mulai' => '19:00', 'jam_selesai' => '20:30', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Algoritma dan Pemrograman 2 (TI/II/2/M)', 'ruangan' => 'Ruang 3/5']);

        // DSN002 - Ahmad Fajri
        JadwalMingguan::create(['dosen_id' => 'DSN002', 'hari' => 'Kamis', 'jam_mulai' => '10:00', 'jam_selesai' => '11:40', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Sistem Tertanam (TI/VI/2/PA)', 'ruangan' => 'Ruang 4/6']);
        JadwalMingguan::create(['dosen_id' => 'DSN002', 'hari' => 'Kamis', 'jam_mulai' => '13:00', 'jam_selesai' => '14:40', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Sistem Tertanam (TI/VI/2/PB)', 'ruangan' => 'Ruang 12']);
        JadwalMingguan::create(['dosen_id' => 'DSN002', 'hari' => 'Rabu', 'jam_mulai' => '20:30', 'jam_selesai' => '22:00', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Sistem Tertanam (TI/VI/2/M)', 'ruangan' => 'Ruang 7']);

        // DSN003 - Rizki Galang Rahmadani
        JadwalMingguan::create(['dosen_id' => 'DSN003', 'hari' => 'Rabu', 'jam_mulai' => '10:00', 'jam_selesai' => '11:40', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Kecerdasan Bisnis (BD/IV/3/P)', 'ruangan' => 'Ruang 10']);
        JadwalMingguan::create(['dosen_id' => 'DSN003', 'hari' => 'Senin', 'jam_mulai' => '13:00', 'jam_selesai' => '14:40', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Bisnis Agile (BD/VI/2/P)', 'ruangan' => 'Ruang 10']);
        JadwalMingguan::create(['dosen_id' => 'DSN003', 'hari' => 'Kamis', 'jam_mulai' => '13:00', 'jam_selesai' => '14:40', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Manajemen Keuangan (BD/VI/3/P)', 'ruangan' => 'Ruang 9']);
        JadwalMingguan::create(['dosen_id' => 'DSN003', 'hari' => 'Kamis', 'jam_mulai' => '13:00', 'jam_selesai' => '14:40', 'kegiatan' => 'Mengajar', 'mata_kuliah' => 'Manajemen Inovasi (BD/VI/2/P)', 'ruangan' => 'Ruang 13']);

        // ============================================================
        // CONTOH: Jadwal Akan Datang (demo)
        // ============================================================
        JadwalAkanDatang::create(['dosen_id' => 'DSN001', 'judul' => 'Seminar Nasional AI', 'tanggal_mulai' => '2026-04-05', 'tanggal_selesai' => '2026-04-05', 'is_fullday' => false, 'jam_mulai' => '09:00', 'jam_selesai' => '12:00', 'keterangan' => 'Sebagai pembicara di Aula STMIK WCD']);
        JadwalAkanDatang::create(['dosen_id' => 'DSN002', 'judul' => 'Workshop IoT', 'tanggal_mulai' => '2026-04-10', 'tanggal_selesai' => '2026-04-12', 'is_fullday' => true, 'keterangan' => 'Pelatihan 3 hari di Bandung']);

        // ============================================================
        // CONTOH: Jadwal Dadakan (demo)
        // ============================================================
        JadwalDadakan::create(['dosen_id' => 'DSN003', 'judul' => 'Sakit', 'tanggal_mulai' => now()->toDateString(), 'tanggal_selesai' => now()->toDateString(), 'is_fullday' => true, 'keterangan' => 'Dosen tidak hadir karena sakit']);
    }
}
