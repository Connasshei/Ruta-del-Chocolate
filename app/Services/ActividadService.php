<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\Evento;
use App\Models\Participacion;
use App\Models\User;
use Carbon\Carbon;

class ActividadService
{
    public function registrarParticipacion(
        Evento $evento,
        Actividad $actividad,
        User $turista,
        int $puntaje,
        int $intentos = 1
    ): Participacion {
        // Validar que turista está inscrito
        if (! $evento->estaInscrito($turista)) {
            throw new \InvalidArgumentException('El turista no está inscrito en este evento.');
        }

        // Verificar que ya no completó esta actividad
        $existente = Participacion::where('evento_id', $evento->id)
            ->where('actividad_id', $actividad->id)
            ->where('user_id', $turista->id)
            ->first();

        if ($existente) {
            // Permitir solo una vez por evento
            throw new \InvalidArgumentException('Ya completaste esta actividad en este evento.');
        }

        // Validar puntuación
        if ($puntaje < 0 || $puntaje > $actividad->puntos_max) {
            throw new \InvalidArgumentException(
                "Puntuación debe estar entre 0 y {$actividad->puntos_max}."
            );
        }

        return Participacion::create([
            'evento_id' => $evento->id,
            'actividad_id' => $actividad->id,
            'user_id' => $turista->id,
            'puntaje' => $puntaje,
            'intentos' => $intentos,
            'completado_en' => Carbon::now(),
        ]);
    }

    public function obtenerParticipacionesEvento(Evento $evento)
    {
        return Participacion::where('evento_id', $evento->id)
            ->with(['actividad.parada', 'turista'])
            ->orderByDesc('puntaje')
            ->get();
    }

    public function obtenerParticipacionesTurista(Evento $evento, User $turista)
    {
        return Participacion::where('evento_id', $evento->id)
            ->where('user_id', $turista->id)
            ->with('actividad.parada')
            ->orderByDesc('created_at')
            ->get();
    }

    public function obtenerRankingEvento(Evento $evento)
    {
        return Participacion::where('evento_id', $evento->id)
            ->with('turista')
            ->groupBy('user_id')
            ->selectRaw('user_id, SUM(puntaje) as puntaje_total, COUNT(*) as actividades_completadas')
            ->orderByDesc('puntaje_total')
            ->get();
    }

    public function obtenerPuntajeTotal(Evento $evento, User $turista): int
    {
        return (int) Participacion::where('evento_id', $evento->id)
            ->where('user_id', $turista->id)
            ->sum('puntaje');
    }

    public function obtenerActividadesCompletadas(Evento $evento, User $turista)
    {
        return Participacion::where('evento_id', $evento->id)
            ->where('user_id', $turista->id)
            ->with('actividad')
            ->get();
    }
}
