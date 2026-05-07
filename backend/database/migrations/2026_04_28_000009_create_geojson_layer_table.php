<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('geojson_layer', function (Blueprint $table) {
            $table->id();
            $table->string('nama_layer');
            $table->enum('jenis_layer', ['ruas', 'rumija', 'rumaja', 'pemanfaatan', 'titik_izin']);
            $table->json('data_geojson');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geojson_layer');
    }
};
