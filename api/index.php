<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Panggil Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// 2. Bawa environment Vercel ke /tmp karena Vercel bersifat Read-Only
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');
@mkdir('/tmp/storage/framework/views', 0777, true);
@mkdir('/tmp/storage/framework/cache/data', 0777, true);
@mkdir('/tmp/storage/framework/sessions', 0777, true);
@mkdir('/tmp/storage/logs', 0777, true);
@mkdir('/tmp/storage/bootstrap/cache', 0777, true);

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_SERVICES_CACHE=/tmp/storage/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/storage/bootstrap/cache/packages.php');

$app->handleRequest(Request::capture());
