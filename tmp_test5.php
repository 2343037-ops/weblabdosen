<?php
$d = \App\Models\Dosen::where('nama', 'LIKE', '%Ivan%')->first();
$akan = $d->jadwalAkanDatang()->where('tanggal_mulai', '<=', \Carbon\Carbon::now()->toDateString())->where('tanggal_selesai', '>=', \Carbon\Carbon::now()->toDateString())->get()->toArray();
print_r($akan);
