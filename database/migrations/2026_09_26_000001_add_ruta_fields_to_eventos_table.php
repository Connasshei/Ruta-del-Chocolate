<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // Tipo de ruta: 'completa', 'rapida', 'personalizada'
            $table->enum('tipo_ruta', ['completa', 'rapida', 'personalizada'])
                ->default('completa')
                ->after('estado');

            // Duración total estimada en minutos (máximo 240 minutos = 4 horas)
            $table->unsignedInteger('duracion_total_minutos')
                ->default(180) // 3 horas por defecto
                ->after('tipo_ruta');

            $table->index('tipo_ruta');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['tipo_ruta', 'duracion_total_minutos']);
        });
    }
};
