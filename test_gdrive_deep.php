<?php
require __DIR__ . '/backend/vendor/autoload.php';
$app = require_once __DIR__ . '/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

// Uji: apakah put() dengan string content berhasil ketika ada path dengan spasi?
$disk = Storage::disk('google');

// Test 1: simple path
echo "Test 1: path tanpa spasi\n";
$result = $disk->put('Dasar Hukum/TestSimple/file.pdf', 'PDF Content Test');
echo "Result: " . ($result ? "SUKSES" : "GAGAL") . "\n\n";

// Test 2: path dengan spasi seperti yang digunakan controller
echo "Test 2: path dengan nama folder yang mengandung titik dan spasi\n";
$result = $disk->put('Dasar Hukum/UU No. 388/dokumen_test.pdf', 'PDF Content Test');
echo "Result: " . ($result ? "SUKSES" : "GAGAL") . "\n\n";

// Test 3: isi file yang lebih besar (simulasi 1MB)
echo "Test 3: content 1MB\n";
$bigContent = str_repeat('A', 1024*1024);
$result = $disk->put('Dasar Hukum/TestBig/big_file.pdf', $bigContent);
echo "Result: " . ($result ? "SUKSES" : "GAGAL") . "\n\n";

// Cek apakah throwsExceptions aktif
echo "ThrowsExceptions mode: ";
try {
    $reflection = new ReflectionClass($disk);
    $throwProp = $reflection->getProperty('throwsExceptions');
    $throwProp->setAccessible(true);
    echo $throwProp->getValue($disk) ? "TRUE (exception dilempar)" : "FALSE (error disenyapkan)";
} catch (Exception $e) {
    echo "Tidak bisa cek: " . $e->getMessage();
}
echo "\n\n";

// Coba dengan throwsExceptions untuk melihat error sebenarnya
echo "Test 4: coba dengan exceptions diaktifkan\n";
try {
    $disk->throwsExceptions();
    $result = $disk->put('Dasar Hukum/TestException/file.pdf', 'PDF Content Test');
    echo "Result: " . ($result ? "SUKSES" : "GAGAL") . "\n";
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
}
