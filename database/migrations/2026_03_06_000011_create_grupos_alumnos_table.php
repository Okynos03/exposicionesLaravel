<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('grupos_alumnos', function (Blueprint $table) {
            $table->foreignId('id_grupo')->constrained('grupos', 'id_grupo')->onDelete('cascade');
            $table->foreignId('id_usuario_alumno')->constrained('alumnos', 'id_usuario')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('grupos_alumnos'); }
};