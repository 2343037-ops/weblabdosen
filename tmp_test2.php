<?php
$ivans = \App\Models\Dosen::where('nama', 'LIKE', '%Ivan%')->get();
$ahmads = \App\Models\Dosen::where('nama', 'LIKE', '%Ahmad Fajri%')->get();
foreach([...$ivans, ...$ahmads] as $d) {
    echo $d->nama . " | Status: " . $d->status . " | Mode: " . $d->status_mode . "\n";
    foreach($d->jadwalMingguan as $j) {
        echo "  - Mingguan: " . $j->hari . " " . $j->jam_mulai . " s/d " . $j->jam_selesai . "\n";
    }
    foreach($d->jadwalDadakan as $j) {
        if ($j->tanggal_mulai == \Carbon\Carbon::now()->format('Y-m-d')) {
            echo "  - Dadakan: " . $j->jam_mulai . " s/d " . $j->jam_selesai . "\n";
        }
    }
}
