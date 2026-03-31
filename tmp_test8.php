<?php
$d1 = \App\Models\Dosen::where('nama', 'LIKE', '%Ivan%')->first();
if ($d1) {
    if ($d1->jadwalAkanDatang) {
        foreach($d1->jadwalAkanDatang as $j) {
            if ($j->is_fullday && $j->tanggal_mulai->toDateString() <= \Carbon\Carbon::now()->toDateString() && $j->tanggal_selesai->toDateString() >= \Carbon\Carbon::now()->toDateString()) {
                $j->delete();
            }
        }
    }
    $d1->status_mode = 'otomatis';
    $d1->save();
    $d1->syncStatusFromJadwal();
}

$d2 = \App\Models\Dosen::where('nama', 'LIKE', '%Ahmad Fajri%')->first();
if ($d2) {
    $d2->status_mode = 'otomatis';
    $d2->save();
    $d2->syncStatusFromJadwal();
}
