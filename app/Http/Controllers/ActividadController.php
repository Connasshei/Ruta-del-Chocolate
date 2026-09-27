<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarParticipacionRequest;
use App\Models\Actividad;
use App\Models\Evento;
use App\Services\ActividadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActividadController extends Controller
{
    public function __construct(private readonly ActividadService $actividadService)
    {
    }

    public function mostrar(Evento $evento, Actividad $actividad): View
    {
        // Validar que está en una parada del evento
        if ($actividad->parada_id !== null) {
            $paradasEventoIds = $evento->eventoParadas()->pluck('parada_id')->toArray();
            if (! in_array($actividad->parada_id, $paradasEventoIds)) {
                abort(404, 'Esta actividad no está en este evento.');
            }
        }

        // Validar que turista está inscrito
        if (! $evento->estaInscrito(Auth::user())) {
            abort(403, 'No estás inscrito en este evento.');
        }

        // Verificar si ya completó
        $yaCompletada = $this->actividadService->obtenerParticipacionesTurista($evento, Auth::user())
            ->where('actividad_id', $actividad->id)
            ->first();

        return view('actividades.mostrar', [
            'evento' => $evento,
            'actividad' => $actividad,
            'yaCompletada' => $yaCompletada,
        ]);
    }

    public function registrarParticipacion(
        RegistrarParticipacionRequest $request,
        Evento $evento,
        Actividad $actividad
    ): JsonResponse {
        try {
            $participacion = $this->actividadService->registrarParticipacion(
                $evento,
                $actividad,
                Auth::user(),
                $request->integer('puntaje'),
                $request->integer('intentos', 1)
            );

            return response()->json([
                'success' => true,
                'message' => '¡Actividad completada!',
                'participacion' => [
                    'id' => $participacion->id,
                    'puntaje' => $participacion->puntaje,
                    'actividad' => $participacion->actividad->nombre,
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function ranking(Evento $evento): View
    {
        // Solo guías pueden ver ranking en tiempo real
        if ($evento->guia_id !== Auth::id() && ! Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $ranking = $this->actividadService->obtenerRankingEvento($evento);
        $participacionesTotales = $evento->participaciones()->count();

        return view('actividades.ranking', [
            'evento' => $evento,
            'ranking' => $ranking,
            'participacionesTotales' => $participacionesTotales,
        ]);
    }

    public function resumenTurista(Evento $evento): View
    {
        // Solo el turista puede ver su resumen
        if (! $evento->estaInscrito(Auth::user())) {
            abort(403);
        }

        $participaciones = $this->actividadService->obtenerParticipacionesTurista($evento, Auth::user());
        $puntajeTotal = $this->actividadService->obtenerPuntajeTotal($evento, Auth::user());
        $ranking = $this->actividadService->obtenerRankingEvento($evento);

        // Encontrar posición del turista
        $posicion = $ranking->search(fn($r) => $r->user_id === Auth::id()) + 1;

        return view('actividades.resumen-turista', [
            'evento' => $evento,
            'participaciones' => $participaciones,
            'puntajeTotal' => $puntajeTotal,
            'posicion' => $posicion,
            'totalTuristas' => $ranking->count(),
        ]);
    }
}
