<?php
require __DIR__ . '/backend/vendor/autoload.php';
$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

try {
    $disk = Storage::disk('google');
    $folderName = 'Dasar Hukum/Test Folder';
    $filename = 'test_upload.txt';
    $filePath = $folderName . '/' . $filename;
    
    // Simulate what Laravel does: put the file contents
    $content = 'This is a test file for Dasar Hukum.';
    $result = $disk->put($filePath, $content);
    
    echo "Upload result for put(): " . ($result ? "Success" : "Failed") . "\n";
    
    if ($result) {
        $url = $disk->url($filePath);
        echo "File URL: " . $url . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
