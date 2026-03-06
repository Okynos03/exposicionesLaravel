<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('maestros', function (Blueprint $table) {
            $table->foreignId('id_usuario')->primary()->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('maestros'); }
};