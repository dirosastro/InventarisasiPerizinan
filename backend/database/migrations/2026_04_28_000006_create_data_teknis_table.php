<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_teknis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')->constrained('perizinan')->onDelete('cascade');
            $table->decimal('panjang_rumija', 10, 2)->nullable();
            $table->decimal('panjang_rumaja', 10, 2)->nullable();
            $table->decimal('panjang_dimanfaatkan', 10, 2)->nullable();
            $table->string('sta_awal')->nullable();
            $table->string('sta_akhir')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_teknis');
    }
};
