<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parada_id')->constrained('paradas')->cascadeOnDelete();
            $table->string('nombre');
            $table->enum('tipo', ['trivia', 'quiz_foto', 'encuentra_diferencia', 'otro'])
                ->default('trivia');
            $table->json('configuracion')->nullable(); // preguntas, opciones, respuesta correcta, etc.
            $table->unsignedInteger('puntos_max')->default(100);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
