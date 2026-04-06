<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(App\Console\Kernel::class)->bootstrap();
$u = App\Models\User::where('email','admin@example.com')->first();
var_export([$u?->email,$u?->password,$u?->hasRole('admin')]);
