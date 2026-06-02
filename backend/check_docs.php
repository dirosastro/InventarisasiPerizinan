<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$id = $argv[1] ?? null;
if (!$id) {
    echo "Usage: php check_docs.php <perizinan_id>\n";
    exit(1);
}

$perizinan = \App\Models\Perizinan::find($id);
if (!$perizinan) {
    echo "Perizinan with ID $id not found.\n";
    exit(1);
}

echo "Perizinan: " . $perizinan->nomor_izin . " (" . $perizinan->pemohon . ")\n";
$docs = \DB::table('dokumen')->where('perizinan_id', $id)->get();
echo "Documents count: " . count($docs) . "\n";
foreach ($docs as $doc) {
    echo "- ID: {$doc->id}, Name: {$doc->nama_file}, Path: {$doc->file_path}\n";
}
