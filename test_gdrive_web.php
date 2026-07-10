<?php
// Pastikan hanya bisa diakses dari localhost
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('Akses ditolak');
}

require __DIR__ . '/backend/vendor/autoload.php';
$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNOSTIK GOOGLE DRIVE VIA WEB (Apache) ===\n";
echo "PHP: " . phpversion() . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n\n";

$disk = Storage::disk('google');

// Test 1: put() string kecil
echo "Test 1: put() string kecil\n";
try {
    $result = $disk->put('Dasar Hukum/WebTest2/file1.txt', 'Hello World via web');
    echo "Result: " . ($result ? "SUKSES" : "GAGAL (false)") . "\n\n";
} catch (\Exception $e) {
    echo "EXCEPTION [" . get_class($e) . "]: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "Caused by: " . $e->getPrevious()->getMessage() . "\n";
    }
    echo "\n";
}

// Test 2: put() file 1MB
echo "Test 2: put() content 1MB\n";
try {
    $bigContent = str_repeat('A', 1024*1024);
    $result = $disk->put('Dasar Hukum/WebTest2/big_file.pdf', $bigContent);
    echo "Result: " . ($result ? "SUKSES" : "GAGAL (false)") . "\n\n";
} catch (\Exception $e) {
    echo "EXCEPTION [" . get_class($e) . "]: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "Caused by [" . get_class($e->getPrevious()) . "]: " . $e->getPrevious()->getMessage() . "\n";
    }
    echo "\n";
}

// Test 3: list files
echo "Test 3: list folder\n";
try {
    $files = $disk->files('Dasar Hukum/WebTest2');
    echo "Files: " . implode(', ', $files) . "\n\n";
} catch (\Exception $e) {
    echo "GAGAL list: " . $e->getMessage() . "\n\n";
}

echo "=== SELESAI ===\n";
