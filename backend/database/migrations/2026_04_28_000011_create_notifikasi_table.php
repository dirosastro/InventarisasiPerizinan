<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')->constrained('perizinan')->onDelete('cascade');
            $table->enum('jenis_notifikasi', ['H-30', 'H-14', 'H-7', 'H-1']);
            $table->date('tanggal_kirim');
            $table->enum('status_kirim', ['pending', 'terkirim'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
