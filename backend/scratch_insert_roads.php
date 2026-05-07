<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$roads = [
    'JLN. ARYA BANJAR GETAS (MATARAM)',
    'JLN. DR. SUJONO (MATARAM)',
    'JLN. SUDIRMAN (MATARAM)',
    'JLN. ENERGI (MATARAM)',
    'JLN. SALEH SUNGKAR 2 (MATARAM)',
    'JLN. TGH. SALEH HAMBALI (DASAN CERMEN - BENGKEL)',
    'JLN. JEND. A. YANI (MATARAM)',
    'JLN. ADI SUCIPTO / SELAPARANG - REMBIGA (JLN.SUDIRMAN)',
    'JLN. SALEH SUNGKAR 1 (MATARAM)',
    'JLN. TGH FAESAL (MATARAM)',
    'JLN. ADI SUCIPTO / AMPENAN - SELAPARANG'
];

$start = 66;
$count = 0;

foreach($roads as $i => $name) {
    $exists = DB::table('ruas_jalan')->where('nama_ruas', $name)->exists();
    if (!$exists) {
        DB::table('ruas_jalan')->insert([
            'nama_ruas' => $name,
            'kode_ruas' => 'R-' . str_pad($start + $count, 3, '0', STR_PAD_LEFT),
            'satker_id' => 1,
            'ppk_id' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $count++;
    }
}

echo "Inserted $count new roads.\n";
