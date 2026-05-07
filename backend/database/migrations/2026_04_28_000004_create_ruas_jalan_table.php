<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruas_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ruas');
            $table->string('kode_ruas')->unique();
            $table->decimal('panjang_km', 8, 2)->nullable();
            $table->foreignId('satker_id')->constrained('satker')->onDelete('cascade');
            $table->foreignId('ppk_id')->constrained('ppk')->onDelete('cascade');
            $table->geometry('geom')->nullable(); // Using geometry type for GeoJSON/PostGIS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruas_jalan');
    }
};
