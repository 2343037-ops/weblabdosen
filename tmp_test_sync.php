<?php
$d1 = \App\Models\Dosen::where('nama', 'LIKE', '%Ivan%')->first();
if ($d1) {
    // Cek jika statusnya sedang ada Jadwal Akan Datang fullday
    $akan = $d1->jadwalAkanDatang()->where('tanggal_mulai', '<=', \Carbon\Carbon::now()->toDateString())->where('tanggal_selesai', '>=', \Carbon\Carbon::now()->toDateString())->get();
    foreach($akan as $j) {
        $j->delete(); // Hapus jadwal hari ini agar otomatis kembali ke "Di Ruangan"
    }
    $d1->status_mode = 'otomatis';
    $d1->save();
    $d1->syncStatusFromJadwal();
    echo 'Ivan Haristyawan disinkronisasi: ' . $d1->status . "\n";
}

$d2 = \App\Models\Dosen::where('nama', 'LIKE', '%Ahmad Fajri%')->first();
if ($d2) {
    $d2->status_mode = 'otomatis';
    $d2->save();
    $d2->syncStatusFromJadwal();
    echo 'Ahmad Fajri disinkronisasi: ' . $d2->status . "\n";
}
