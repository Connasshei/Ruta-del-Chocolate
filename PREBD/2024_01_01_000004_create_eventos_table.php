<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('guia_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->unsignedInteger('cupo_maximo')->default(30);
            $table->enum('estado', ['programado', 'en_curso', 'finalizado', 'cancelado'])
                ->default('programado');
            $table->timestamps();

            $table->index(['fecha', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
