<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('puntaje')->default(0);
            $table->unsignedInteger('intentos')->default(1);
            $table->timestamp('completado_en')->nullable();
            $table->timestamps();

            $table->unique(['evento_id', 'actividad_id', 'user_id']);
            // Índice clave para calcular el ranking en vivo por evento
            $table->index(['evento_id', 'puntaje']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participaciones');
    }
};
