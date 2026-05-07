<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Perizinan;
use Carbon\Carbon;

$targetDate = Carbon::now()->addDays(30)->toDateString();
Perizinan::where('id', 1)->update(['tanggal_akhir' => $targetDate, 'no_hp' => '6281234567890']);
echo "Updated permit 1 to expire in 30 days with phone number 6281234567890\n";
