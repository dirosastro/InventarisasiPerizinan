<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perizinan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_izin')->unique();
            $table->enum('jenis_izin', ['rekomendasi', 'izin', 'dispensasi']);
            $table->string('sub_jenis')->nullable();
            $table->string('icon')->nullable();
            $table->string('pemohon');
            $table->foreignId('satker_id')->constrained('satker')->onDelete('cascade');
            $table->date('tanggal_terbit');
            $table->date('tanggal_akhir');
            $table->enum('status', ['aktif', 'hampir_habis', 'kadaluarsa'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perizinan');
    }
};
