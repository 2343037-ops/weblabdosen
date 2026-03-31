<?php
$d = App\Models\Dosen::first();
if (!$d) { echo "No dosen.\n"; exit; }
$j = $d->jadwalMingguan()->create([
    'hari' => 'Senin',
    'jam_mulai' => '10:00:00',
    'jam_selesai' => '12:00:00',
    'kegiatan' => 'Testing DELETE'
]);
echo "DOSEN_ID=" . $d->id . "\n";
echo "JADWAL_ID=" . $j->id . "\n";
