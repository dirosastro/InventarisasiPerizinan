<?php
require __DIR__ . '/backend/vendor/autoload.php';
$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

try {
    $disk = Storage::disk('google');
    $folderName = 'Dasar Hukum/Test Folder 3';
    $filename = 'large_test_upload.pdf';
    
    // Create a local dummy file of 2MB
    $localPath = __DIR__ . '/dummy_2mb.pdf';
    $content = str_repeat('0123456789', 200000); // ~2MB
    file_put_contents($localPath, $content);
    
    // Simulate what putFileAs does
    $fileObj = new File($localPath);
    echo "Uploading " . $fileObj->getSize() . " bytes...\n";
    $start = microtime(true);
    
    $result = $disk->putFileAs($folderName, $fileObj, $filename);
    
    $end = microtime(true);
    echo "Upload result: " . var_export($result, true) . "\n";
    echo "Time taken: " . ($end - $start) . " seconds\n";
    
    if ($result) {
        $url = $disk->url($result);
        echo "File URL: " . $url . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
