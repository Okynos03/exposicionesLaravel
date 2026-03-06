<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('criterios', function (Blueprint $table) {
            $table->id('id_criterios');
            $table->foreignId('id_rubrica')->constrained('rubricas', 'id_rubrica')->onDelete('cascade');
            $table->text('descripcion');
            $table->decimal('porcentaje', 5, 2);
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('criterios'); }
};