<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('exposiciones', function (Blueprint $table) {
            $table->id('id_expo');
            $table->foreignId('id_equipo')->constrained('equipos', 'id_equipo');
            $table->foreignId('id_rubrica')->constrained('rubricas', 'id_rubrica');
            $table->string('tema');
            $table->dateTime('fecha');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('exposiciones'); }
};