<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $tour = Tour::firstOrCreate(
            ['slug' => 'ruta-del-chocolate'],
            [
                'nombre' => 'La Ruta del Chocolate',
                'descripcion' => 'Recorrido por los mejores lugares del chocolate en la ciudad de Sucre: historia, cacao, fabricación y degustación.',
                'imagen_portada' => 'tours/ruta-del-chocolate.jpg',
                'activo' => true,
            ]
        );

        $paradas = [
            [
                'nombre' => 'Plaza 25 de Mayo',
                'descripcion' => 'Punto de encuentro del recorrido.',
                'orden' => 1,
                'latitud' => -19.0473,
                'longitud' => -65.2600,
                'imagen_principal' => 'paradas/plaza-25-de-mayo.jpg',
            ],
            [
                'nombre' => 'Fábrica de Chocolate Artesanal',
                'descripcion' => 'Visita al taller de producción y degustación de cacao sucrense.',
                'orden' => 2,
                'latitud' => -19.0491,
                'longitud' => -65.2591,
                'imagen_principal' => 'paradas/fabrica-chocolate.jpg',
            ],
            [
                'nombre' => 'Museo del Chocolate',
                'descripcion' => 'Historia del cacao y del chocolate en Bolivia.',
                'orden' => 3,
                'latitud' => -19.0500,
                'longitud' => -65.2580,
                'imagen_principal' => 'paradas/museo-chocolate.jpg',
            ],
            [
                'nombre' => 'Taller de Chocolatería',
                'descripcion' => 'Elaboración de bombones con cacao boliviano.',
                'orden' => 4,
                'latitud' => -19.0510,
                'longitud' => -65.2570,
                'imagen_principal' => 'paradas/taller-chocolateria.jpg',
            ],
        ];

        $actividades = [
            // keyed by nombre de parada
            'Plaza 25 de Mayo' => [
                ['nombre' => 'Trivia: ¿cuánto sabes de Sucre?', 'tipo' => Actividad::TIPO_TRIVIA, 'puntos_max' => 100,
                 'configuracion' => ['preguntas' => [
                     ['pregunta' => '¿En qué departamento se encuentra Sucre?', 'opciones' => ['La Paz', 'Chuquisaca', 'Cochabamba', 'Santa Cruz'], 'respuesta' => 1],
                 ]]],
            ],
            'Fábrica de Chocolate Artesanal' => [
                ['nombre' => 'Quiz foto: identifica el ingrediente', 'tipo' => Actividad::TIPO_QUIZ_FOTO, 'puntos_max' => 100,
                 'configuracion' => ['preguntas' => [
                     ['pregunta' => '¿Cuál de estos es cacao puro?', 'opciones' => ['Manteca', 'Cacao', 'Azúcar', 'Leche'], 'respuesta' => 1],
                 ]]],
                ['nombre' => 'Encuentra la diferencia', 'tipo' => Actividad::TIPO_ENCUENTRA_DIFERENCIA, 'puntos_max' => 50,
                 'configuracion' => ['diferencias' => 3]],
            ],
            'Museo del Chocolate' => [
                ['nombre' => 'Trivia: historia del cacao', 'tipo' => Actividad::TIPO_TRIVIA, 'puntos_max' => 100,
                 'configuracion' => ['preguntas' => [
                     ['pregunta' => 'El cacao es originario de...', 'opciones' => ['Europa', 'África', 'América', 'Asia'], 'respuesta' => 2],
                 ]]],
                ['nombre' => 'Quiz foto: ordena la línea de producción', 'tipo' => Actividad::TIPO_QUIZ_FOTO, 'puntos_max' => 80,
                 'configuracion' => ['pasos' => 4]],
            ],
            'Taller de Chocolatería' => [
                ['nombre' => 'Trivia: el bombón perfecto', 'tipo' => Actividad::TIPO_TRIVIA, 'puntos_max' => 100,
                 'configuracion' => ['preguntas' => [
                     ['pregunta' => '¿Qué se usa para templar el chocolate?', 'opciones' => ['Agua', 'Baño María', 'Horno', 'Microondas'], 'respuesta' => 1],
                 ]]],
            ],
        ];

        foreach ($paradas as $datosParada) {
            $parada = $tour->paradas()->firstOrCreate(
                ['nombre' => $datosParada['nombre']],
                $datosParada
            );

            foreach ($actividades[$datosParada['nombre']] ?? [] as $datosActividad) {
                $parada->actividades()->firstOrCreate(
                    ['nombre' => $datosActividad['nombre']],
                    $datosActividad
                );
            }
        }
    }
}