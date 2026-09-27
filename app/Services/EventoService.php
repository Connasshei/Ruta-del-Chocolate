<?php

namespace App\Services;

use App\Exceptions\ReservaNoDisponibleException;
use App\Models\Evento;
use App\Models\EventoParada;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EventoService
{
    public function crear(User $guia, array $datos, array $ruta): Evento
    {
        return DB::transaction(function () use ($guia, $datos, $ruta) {
            $evento = Evento::create($datos + [
                'guia_id' => $guia->id,
                'estado' => Evento::ESTADO_PROGRAMADO,
            ]);

            $this->guardarRuta($evento, $ruta);

            return $evento->load(['tour', 'paradas']);
        });
    }

    public function actualizar(Evento $evento, array $datos, array $ruta): Evento
    {
        return DB::transaction(function () use ($evento, $datos, $ruta) {
            $evento->update($datos);

            $this->guardarRuta($evento, $ruta);

            return $evento->load(['tour', 'paradas']);
        });
    }

    public function cambiarEstado(Evento $evento, string $estado): Evento
    {
        if ($estado === $evento->estado) {
            return $evento;
        }

        if (! $evento->puedeTransicionarA($estado)) {
            throw ReservaNoDisponibleException::transicionInvalida($evento->estado, $estado);
        }

        DB::transaction(function () use ($evento, $estado) {
            $evento->update(['estado' => $estado]);

            match ($estado) {
                Evento::ESTADO_EN_CURSO => $this->iniciarRecorrido($evento),
                Evento::ESTADO_FINALIZADO => $this->cerrarRecorrido($evento),
                Evento::ESTADO_CANCELADO => $this->liberarInscripciones($evento),
                default => null,
            };
        });

        return $evento->refresh();
    }

    /**
     * Reemplaza la ruta del evento conservando el avance de cada parada que ya estaba en el recorrido.
     *
     * @param  array<int, array{parada_id: int, orden: int, hora_estimada: ?string, duracion_minutos: int, actividades_ids: ?array, notas_guia: ?string}>  $ruta
     */
    private function guardarRuta(Evento $evento, array $ruta): void
    {
        $avance = $evento->eventoParadas->keyBy('parada_id');

        $evento->eventoParadas()->delete();

        foreach ($ruta as $item) {
            $parada_id = $item['parada_id'];
            $estadoAnterior = $avance->get($parada_id)?->estado ?? EventoParada::ESTADO_PENDIENTE;

            $evento->eventoParadas()->create([
                'parada_id' => $parada_id,
                'orden' => $item['orden'],
                'hora_estimada' => $item['hora_estimada'] ?? null,
                'duracion_minutos' => $item['duracion_minutos'] ?? 45,
                'actividades_ids' => $item['actividades_ids'] ?? null,
                'notas_guia' => $item['notas_guia'] ?? null,
                'estado' => $estadoAnterior,
            ]);
        }
    }

    private function iniciarRecorrido(Evento $evento): void
    {
        $evento->eventoParadas()->where('orden', 1)->update(['estado' => EventoParada::ESTADO_EN_CURSO]);
    }

    private function cerrarRecorrido(Evento $evento): void
    {
        $evento->eventoParadas()->update(['estado' => EventoParada::ESTADO_COMPLETADA]);
    }

    private function liberarInscripciones(Evento $evento): void
    {
        $evento->inscripcionesActivas()->update(['estado' => Inscripcion::ESTADO_CANCELADO]);
    }
}
