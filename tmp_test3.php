<?php
$d = \App\Models\Dosen::where('nama', 'LIKE', '%Ivan%')->first();
$controller = new \App\Http\Controllers\AdminController();
$controller->syncDosenStatus($d);
$d->refresh();
echo "Status Ivan after sync: " . $d->status . "\n";
