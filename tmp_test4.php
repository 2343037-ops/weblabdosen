<?php
$d = \App\Models\Dosen::where('nama', 'LIKE', '%Ivan%')->first();
$now = \Carbon\Carbon::now();
$today = $now->toDateString();
$jam = $now->format('H:i:s');
$hari = 'Selasa';

$d1 = $d->jadwalDadakan()->where('tanggal_mulai', '<=', $today)->where('tanggal_selesai', '>=', $today)->where('is_fullday', true)->exists();
$d2 = $d->jadwalDadakan()->where('tanggal_mulai', '<=', $today)->where('tanggal_selesai', '>=', $today)->where('is_fullday', false)->where('jam_mulai', '<=', $jam)->where('jam_selesai', '>=', $jam)->exists();
$a1 = $d->jadwalAkanDatang()->where('tanggal_mulai', '<=', $today)->where('tanggal_selesai', '>=', $today)->where('is_fullday', true)->exists();
$a2 = $d->jadwalAkanDatang()->where('tanggal_mulai', '<=', $today)->where('tanggal_selesai', '>=', $today)->where('is_fullday', false)->where('jam_mulai', '<=', $jam)->where('jam_selesai', '>=', $jam)->exists();
$m1 = $d->jadwalMingguan()->where('hari', $hari)->where('jam_mulai', '<=', $jam)->where('jam_selesai', '>=', $jam)->exists();
echo "d1: " . (int)$d1 . "\n";
echo "d2: " . (int)$d2 . "\n";
echo "a1: " . (int)$a1 . "\n";
echo "a2: " . (int)$a2 . "\n";
echo "m1: " . (int)$m1 . "\n";

$d_ahmad = \App\Models\Dosen::where('nama', 'LIKE', '%Ahmad Fajri%')->first();
echo "Ahmad status_mode: " . $d_ahmad->status_mode . "\n";
