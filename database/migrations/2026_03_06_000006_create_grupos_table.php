<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id('id_grupo');
            $table->foreignId('id_materia')->constrained('materias', 'id_materia');
            $table->foreignId('id_maestro')->constrained('maestros', 'id_usuario');
            $table->string('grupo', 10);
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('grupos'); }
};