<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id('id_equipo');
            $table->foreignId('id_grupo')->constrained('grupos', 'id_grupo')->onDelete('cascade');
            $table->string('equipo');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('equipos'); }
};