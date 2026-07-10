<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Storage;

try {
    echo "Testing connection to Google Drive...\n";
    $files = Storage::disk('google')->files('/');
    echo "Successfully connected to Google Drive!\nFiles at root:\n";
    print_r($files);
} catch (\Exception $e) {
    echo "Google Drive connection failed with error:\n";
    echo $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
