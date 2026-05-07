<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')->constrained('perizinan')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('file_path');
            $table->enum('tipe_dokumen', ['jaminan_pelaksanaan', 'izin', 'lainnya'])->default('lainnya');
            $table->integer('ukuran_file')->comment('Size in KB');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
