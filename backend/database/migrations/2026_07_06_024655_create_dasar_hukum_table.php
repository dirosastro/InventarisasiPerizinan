<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dasar_hukum', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['uu', 'pp', 'pm', 'se']);
            $table->string('nomor');
            $table->string('judul');
            $table->text('ringkasan');
            $table->smallInteger('tahun');
            $table->string('link_file_id')->nullable();
            $table->string('link_file_url')->nullable();
            $table->string('sop_file_id')->nullable();
            $table->string('sop_file_url')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dasar_hukum');
    }
};
