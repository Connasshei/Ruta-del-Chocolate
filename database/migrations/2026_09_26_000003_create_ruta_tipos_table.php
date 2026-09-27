<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla para definir tipos de rutas preestablecidas
        Schema::create('ruta_tipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->string('nombre'); // 'Ruta Completa', 'Ruta Rápida', etc.
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('duracion_minutos')->default(180); // duración estimada
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['tour_id', 'nombre']);
            $table->index('activo');
        });

        // Tabla para definir qué paradas van en cada tipo de ruta
        Schema::create('ruta_tipo_parada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_tipo_id')->constrained('ruta_tipos')->cascadeOnDelete();
            $table->foreignId('parada_id')->constrained('paradas')->cascadeOnDelete();
            $table->unsignedInteger('orden'); // orden en la ruta
            $table->unsignedInteger('duracion_minutos')->default(45); // tiempo para esta parada
            $table->json('actividades_ids')->nullable(); // IDs de actividades recomendadas
            $table->timestamps();

            $table->unique(['ruta_tipo_id', 'parada_id']);
            $table->index(['ruta_tipo_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_tipo_parada');
        Schema::dropIfExists('ruta_tipos');
    }
};
