<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('equipo_integrantes', function (Blueprint $table) {
            $table->foreignId('id_equipo')->constrained('equipos', 'id_equipo')->onDelete('cascade');
            $table->foreignId('id_usuario_alumno')->constrained('alumnos', 'id_usuario')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('equipo_integrantes'); }
};