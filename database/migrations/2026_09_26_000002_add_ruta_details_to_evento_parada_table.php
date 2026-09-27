<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evento_parada', function (Blueprint $table) {
            // Tiempo dedicado a esta parada en minutos
            $table->unsignedInteger('duracion_minutos')
                ->nullable()
                ->after('orden');

            // IDs de actividades seleccionadas para esta parada (JSON)
            $table->json('actividades_ids')
                ->nullable()
                ->after('duracion_minutos');

            // Notas del guía para esta parada en este evento
            $table->text('notas_guia')
                ->nullable()
                ->after('actividades_ids');

            // Cambiar estado a saltada
            $table->dropColumn('estado');
            $table->enum('estado', ['pendiente', 'en_curso', 'completada', 'saltada'])
                ->default('pendiente')
                ->after('notas_guia');
        });
    }

    public function down(): void
    {
        Schema::table('evento_parada', function (Blueprint $table) {
            $table->dropColumn(['duracion_minutos', 'actividades_ids', 'notas_guia']);
            $table->dropColumn('estado');
            $table->enum('estado', ['pendiente', 'en_curso', 'completada'])->default('pendiente');
        });
    }
};
