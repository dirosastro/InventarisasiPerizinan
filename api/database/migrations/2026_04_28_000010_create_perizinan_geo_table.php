<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perizinan_geo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')->constrained('perizinan')->onDelete('cascade');
            $table->json('geojson');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perizinan_geo');
    }
};
