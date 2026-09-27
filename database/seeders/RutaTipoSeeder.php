<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Parada;
use App\Models\RutaTipo;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class RutaTipoSeeder extends Seeder
{
    public function run(): void
    {
        $tour = Tour::firstOrCreate(
            ['slug' => 'ruta-chocolate-sucre'],
            [
                'nombre' => 'La Ruta del Chocolate - Sucre',
                'descripcion' => 'Una experiencia única por las chocolaterías más destacadas de Sucre',
                'activo' => true,
            ]
        );

        // Paradas del tour (asumiendo que ya existen)
        $paradas = Parada::where('tour_id', $tour->id)->get();

        if ($paradas->isEmpty()) {
            return; // No crear rutas si no hay paradas
        }

        // ============ RUTA COMPLETA (3-4 horas) ============
        $rutaCompleta = RutaTipo::create([
            'tour_id' => $tour->id,
            'nombre' => 'Ruta Completa',
            'descripcion' => 'Visita todas las chocolaterías con experiencia completa',
            'duracion_minutos' => 240, // 4 horas
            'activo' => true,
        ]);

        // Agregar paradas a ruta completa (orden importa)
        foreach ($paradas as $index => $parada) {
            $rutaCompleta->paradas()->create([
                'parada_id' => $parada->id,
                'orden' => $index + 1,
                'duracion_minutos' => 50,
                'actividades_ids' => $parada->actividades()
                    ->where('activo', true)
                    ->pluck('id')
                    ->toArray(),
            ]);
        }

        // ============ RUTA RÁPIDA (2-3 horas) ============
        // Solo las paradas principales (Taboada y Para Ti)
        $rutaRapida = RutaTipo::create([
            'tour_id' => $tour->id,
            'nombre' => 'Ruta Rápida',
            'descripcion' => 'Visita los destinos turísticos principales (Museo y Fábrica)',
            'duracion_minutos' => 150, // 2.5 horas
            'activo' => true,
        ]);

        // Filtrar paradas principales (Taboada y Para Ti)
        $paradasPrincipales = $paradas->filter(
            fn ($p) => str_contains(strtolower($p->nombre), 'taboada') ||
                      str_contains(strtolower($p->nombre), 'para ti')
        );

        foreach ($paradasPrincipales as $index => $parada) {
            $rutaRapida->paradas()->create([
                'parada_id' => $parada->id,
                'orden' => $index + 1,
                'duracion_minutos' => 70,
                // Solo las 2 primeras actividades
                'actividades_ids' => $parada->actividades()
                    ->where('activo', true)
                    ->limit(2)
                    ->pluck('id')
                    ->toArray(),
            ]);
        }

        // ============ RUTA CENTROS COMERCIALES (1.5-2 horas) ============
        // Solo sucursales del centro
        $rutaCentros = RutaTipo::create([
            'tour_id' => $tour->id,
            'nombre' => 'Ruta Centro',
            'descripcion' => 'Sucursales cercanas en el centro de la ciudad',
            'duracion_minutos' => 120, // 2 horas
            'activo' => true,
        ]);

        $paradasCentro = $paradas->filter(
            fn ($p) => str_contains(strtolower($p->nombre), 'centro') ||
                      str_contains(strtolower($p->nombre), 'sucursal')
        );

        foreach ($paradasCentro as $index => $parada) {
            $rutaCentros->paradas()->create([
                'parada_id' => $parada->id,
                'orden' => $index + 1,
                'duracion_minutos' => 40,
                'actividades_ids' => $parada->actividades()
                    ->where('activo', true)
                    ->limit(1)
                    ->pluck('id')
                    ->toArray(),
            ]);
        }
    }
}
