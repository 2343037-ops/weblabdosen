<?php
// Bawa environment Vercel ke /tmp karena Vercel bersifat Read-Only
$app = require __DIR__.'/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');
@mkdir('/tmp/storage/framework/views', 0777, true);
@mkdir('/tmp/storage/framework/cache', 0777, true);
@mkdir('/tmp/storage/framework/cache/data', 0777, true);
@mkdir('/tmp/storage/framework/sessions', 0777, true);
@mkdir('/tmp/storage/logs', 0777, true);

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
