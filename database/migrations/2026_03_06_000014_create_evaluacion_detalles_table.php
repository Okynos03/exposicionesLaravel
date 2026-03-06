<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('evaluacion_detalles', function (Blueprint $table) {
            $table->foreignId('id_evaluacion')->constrained('evaluaciones', 'id_evaluacion')->onDelete('cascade');
            $table->foreignId('id_criterios')->constrained('criterios', 'id_criterios');
            $table->decimal('calificacion', 5, 2);
        });
    }
    public function down() { Schema::dropIfExists('evaluacion_detalles'); }
};