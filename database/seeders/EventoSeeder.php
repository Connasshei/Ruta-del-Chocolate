<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\EventoParada;
use App\Models\Foto;
use App\Models\Inscripcion;
use App\Models\Participacion;
use App\Models\Tour;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        $tour = Tour::where('slug', 'ruta-del-chocolate')->firstOrFail();
        $guia = User::role('guia')->firstOrFail();
        $turistas = User::role('turista')->get();

        $this->crearEvento($guia, $tour, Carbon::now()->addDays(7), '09:00', Evento::ESTADO_PROGRAMADO);
        $this->crearEvento($guia, $tour, Carbon::today(), '09:00', Evento::ESTADO_EN_CURSO);
        $finalizado = $this->crearEvento($guia, $tour, Carbon::today()->subDays(10), '09:00', Evento::ESTADO_FINALIZADO);

        $this->poblarEventoFinalizado($finalizado, $turistas);
    }

    private function crearEvento(User $guia, Tour $tour, Carbon $fecha, string $hora, string $estado): Evento
    {
        $evento = Evento::create([
            'tour_id' => $tour->id,
            'guia_id' => $guia->id,
            'fecha' => $fecha,
            'hora_inicio' => $hora,
            'cupo_maximo' => 30,
            'estado' => $estado,
        ]);

        $orden = 1;
        $horaBase = (int) substr($hora, 0, 2);

        foreach ($tour->paradas()->orderBy('orden')->get() as $parada) {
            $evento->eventoParadas()->create([
                'parada_id' => $parada->id,
                'orden' => $orden,
                'estado' => $estado === Evento::ESTADO_FINALIZADO ? EventoParada::ESTADO_COMPLETADA : EventoParada::ESTADO_PENDIENTE,
                'hora_estimada' => sprintf('%02d:30', $horaBase + $orden),
            ]);
            $orden++;
        }

        return $evento;
    }

    private function poblarEventoFinalizado(Evento $evento, Collection $turistas): void
    {
        $paradas = $evento->tour()->first()->paradas()->orderBy('orden')->pluck('id');

        $indice = 0;
        foreach ($evento->paradas()->get() as $parada) {
            foreach ($parada->actividades()->get() as $actividad) {
                $turista = $turistas->get($indice % $turistas->count());
                Participacion::firstOrCreate(
                    ['evento_id' => $evento->id, 'actividad_id' => $actividad->id, 'user_id' => $turista->id],
                    ['puntaje' => 100 - ($indice % 3) * 10, 'intentos' => 1, 'completado_en' => Carbon::now()]
                );
                $indice++;
            }
        }

        foreach ($turistas as $k => $turista) {
            Inscripcion::firstOrCreate(
                ['evento_id' => $evento->id, 'user_id' => $turista->id],
                ['estado' => Inscripcion::ESTADO_CONFIRMADO]
            );

            Foto::firstOrCreate(
                ['evento_id' => $evento->id, 'user_id' => $turista->id, 'tipo' => Foto::TIPO_ACTIVIDAD, 'parada_id' => $paradas->get($k % $paradas->count())],
                ['ruta_archivo' => "fotos/eventos/{$evento->id}/turista{$k}-actividad.jpg"]
            );

            Foto::firstOrCreate(
                ['evento_id' => $evento->id, 'user_id' => $turista->id, 'tipo' => Foto::TIPO_LIBRE, 'parada_id' => null],
                ['ruta_archivo' => "fotos/eventos/{$evento->id}/turista{$k}-libre.jpg"]
            );
        }
    }
}