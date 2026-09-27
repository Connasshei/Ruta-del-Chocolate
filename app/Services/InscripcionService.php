<?php

namespace App\Services;

use App\Exceptions\ReservaNoDisponibleException;
use App\Models\Evento;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InscripcionService
{
    public function registrar(User $turista, Evento $evento): Inscripcion
    {
        return DB::transaction(function () use ($turista, $evento) {
            $evento = Evento::query()->lockForUpdate()->findOrFail($evento->id);

            $inscripcion = $evento->inscripciones()->where('user_id', $turista->id)->first();

            if ($inscripcion?->ocupaCupo()) {
                throw ReservaNoDisponibleException::yaInscrito();
            }

            $this->verificarDisponibilidad($evento);

            return $inscripcion
                ? $this->reactivar($inscripcion)
                : $this->crear($evento, $turista);
        });
    }

    public function confirmar(Inscripcion $inscripcion): Inscripcion
    {
        if ($inscripcion->ocupaCupo()) {
            return $inscripcion;
        }

        DB::transaction(function () use ($inscripcion) {
            $evento = $inscripcion->evento()->lockForUpdate()->firstOrFail();

            $this->verificarDisponibilidad($evento);

            $inscripcion->update(['estado' => Inscripcion::ESTADO_CONFIRMADO]);
        });

        return $inscripcion;
    }

    public function cancelar(Inscripcion $inscripcion): Inscripcion
    {
        if ($inscripcion->estaCancelada()) {
            return $inscripcion;
        }

        if ($inscripcion->evento->estado === Evento::ESTADO_FINALIZADO) {
            throw ReservaNoDisponibleException::eventoCerrado(Evento::ESTADO_FINALIZADO);
        }

        $inscripcion->update(['estado' => Inscripcion::ESTADO_CANCELADO]);

        return $inscripcion;
    }

    private function verificarDisponibilidad(Evento $evento): void
    {
        if (! in_array($evento->estado, [Evento::ESTADO_PROGRAMADO, Evento::ESTADO_EN_CURSO], true)) {
            throw ReservaNoDisponibleException::eventoCerrado($evento->estado);
        }

        if ($evento->estaLleno()) {
            throw ReservaNoDisponibleException::eventoLleno();
        }
    }

    private function crear(Evento $evento, User $turista): Inscripcion
    {
        return $evento->inscripciones()->create([
            'user_id' => $turista->id,
            'estado' => Inscripcion::ESTADO_CONFIRMADO,
            'fecha_inscripcion' => now(),
        ]);
    }

    private function reactivar(Inscripcion $inscripcion): Inscripcion
    {
        $inscripcion->update([
            'estado' => Inscripcion::ESTADO_CONFIRMADO,
            'fecha_inscripcion' => now(),
        ]);

        return $inscripcion;
    }
}
