<?php
// Script de debug: imprime o valor de site.solo_mode
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

echo Setting::get('site.solo_mode', 'NULL') . PHP_EOL;
