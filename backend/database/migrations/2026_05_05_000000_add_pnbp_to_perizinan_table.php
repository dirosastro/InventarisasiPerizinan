<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perizinan', function (Blueprint $table) {
            $table->decimal('pnbp', 15, 2)->default(0)->after('tanggal_akhir');
        });
    }

    public function down(): void
    {
        Schema::table('perizinan', function (Blueprint $table) {
            $table->dropColumn('pnbp');
        });
    }
};
