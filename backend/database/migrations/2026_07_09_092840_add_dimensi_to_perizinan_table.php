<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('perizinan', function (Blueprint $table) {
            $table->decimal('panjang', 10, 2)->nullable()->after('pnbp');
            $table->decimal('lebar', 10, 2)->nullable()->after('panjang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perizinan', function (Blueprint $table) {
            $table->dropColumn(['panjang', 'lebar']);
        });
    }
};
