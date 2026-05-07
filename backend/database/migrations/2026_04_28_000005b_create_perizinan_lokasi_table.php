<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perizinan_lokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')->constrained('perizinan')->onDelete('cascade');
            $table->foreignId('satker_id')->constrained('satker')->onDelete('cascade');
            $table->foreignId('ppk_id')->constrained('ppk')->onDelete('cascade');
            $table->text('nama_ruas_jalan');
            $table->string('sta_awal')->nullable();
            $table->string('sta_akhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perizinan_lokasi');
    }
};
