<?php
require __DIR__ . '/backend/vendor/autoload.php';
$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

try {
    $disk = Storage::disk('google');
    $folderName = 'Dasar Hukum/Test Folder 2';
    $filename = 'test_upload_file.txt';
    $filePath = $folderName . '/' . $filename;
    
    // Create a local dummy file
    $localPath = __DIR__ . '/dummy.txt';
    file_put_contents($localPath, 'This is a local file test.');
    
    // Simulate what storeAs does
    $fileObj = new File($localPath);
    $result = $disk->putFileAs($folderName, $fileObj, $filename);
    
    echo "Upload result for putFileAs(): " . var_export($result, true) . "\n";
    
    if ($result) {
        $url = $disk->url($result);
        echo "File URL: " . $url . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
