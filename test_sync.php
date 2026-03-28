<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Dosen;
use App\Models\JadwalMingguan;
use App\Models\JadwalAkanDatang;
use App\Models\JadwalDadakan;
use Carbon\Carbon;

$results = [];

// 1. Setup Dosen
$dosen = Dosen::first();
if (!$dosen) die("Tidak ada dosen di database.");

JadwalMingguan::where('dosen_id', $dosen->id)->delete();
JadwalAkanDatang::where('dosen_id', $dosen->id)->delete();
JadwalDadakan::where('dosen_id', $dosen->id)->delete();

$dosen->status_mode = 'otomatis';
$dosen->save();

$testTime = Carbon::create(2024, 5, 14, 10, 0, 0); 
Carbon::setTestNow($testTime);

$dosen->syncStatusFromJadwal();
$results[] = ["Skenario 1: Tanpa jadwal (Selasa 10:00)", "Ekspektasi: Di Ruangan", "Realita: " . $dosen->status];

$jadwalMingguan = JadwalMingguan::create(['dosen_id' => $dosen->id, 'hari' => 'Selasa', 'jam_mulai' => '09:00:00', 'jam_selesai' => '11:00:00', 'kegiatan' => 'Mengajar']);
$dosen->syncStatusFromJadwal();
$results[] = ["Skenario 2: Tambah Mingguan (Aktif 09:00-11:00)", "Ekspektasi: Tidak Di Ruangan", "Realita: " . $dosen->status];

$jadwalMingguan->update(['jam_mulai' => '13:00:00', 'jam_selesai' => '15:00:00']);
$dosen->syncStatusFromJadwal();
$results[] = ["Skenario 3: Edit Mingguan (Tidak aktif 13:00-15:00)", "Ekspektasi: Di Ruangan", "Realita: " . $dosen->status];

$jadwalAkanDatang = JadwalAkanDatang::create(['dosen_id' => $dosen->id, 'judul' => 'Cuti', 'tanggal_mulai' => $testTime->toDateString(), 'tanggal_selesai' => $testTime->toDateString(), 'is_fullday' => true]);
$dosen->syncStatusFromJadwal();
$results[] = ["Skenario 4: Tambah Akan Datang (Fullday)", "Ekspektasi: Tidak Di Ruangan", "Realita: " . $dosen->status];

$jadwalAkanDatang->update(['is_fullday' => false, 'jam_mulai' => '15:00:00', 'jam_selesai' => '17:00:00']);
$dosen->syncStatusFromJadwal();
$results[] = ["Skenario 5: Edit Akan Datang (Jam 15:00)", "Ekspektasi: Di Ruangan", "Realita: " . $dosen->status];

$dosen->status_mode = 'manual';
$dosen->status = 'Tidak Di Ruangan';
$dosen->save();
$jadwalAkanDatang->update(['jam_mulai' => '09:00:00', 'jam_selesai' => '11:00:00']);
$dosen->syncStatusFromJadwal();
$results[] = ["Skenario 6: Mode Manual Edit Jadwal", "Ekspektasi: Tidak Di Ruangan", "Realita: " . $dosen->status];

$dosen->status_mode = 'otomatis';
$dosen->save();
$dosen->syncStatusFromJadwal();
$jadwalDadakan = JadwalDadakan::create(['dosen_id' => $dosen->id, 'judul' => 'Rapat', 'tanggal_mulai' => $testTime->toDateString(), 'tanggal_selesai' => $testTime->toDateString(), 'is_fullday' => false, 'jam_mulai' => '09:30:00', 'jam_selesai' => '11:30:00']);
$dosen->syncStatusFromJadwal();
$results[] = ["Skenario 7: Tambah Dadakan (Aktif)", "Ekspektasi: Tidak Di Ruangan", "Realita: " . $dosen->status];

$jadwalDadakan->update(['jam_mulai' => '08:00:00', 'jam_selesai' => '09:30:00']);
$jadwalAkanDatang->delete();
$dosen->syncStatusFromJadwal();
$results[] = ["Skenario 8: Edit Dadakan (Selesai)", "Ekspektasi: Di Ruangan", "Realita: " . $dosen->status];

file_put_contents('test_sync_output.json', json_encode($results, JSON_PRETTY_PRINT));
Carbon::setTestNow();
