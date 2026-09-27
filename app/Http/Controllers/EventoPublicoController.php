<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventoPublicoController extends Controller
{
    public function index(Request $request): View
    {
        $estado = $this->estadoValido($request->string('estado')->toString());

        $eventos = Evento::query()
            ->when(
                $estado,
                fn ($query) => $query->where('estado', $estado),
                fn ($query) => $query->abiertosALasInscripciones()
            )
            ->when(
                $request->filled('tour_id'),
                fn ($query) => $query->where('tour_id', $request->integer('tour_id'))
            )
            ->proximos()
            ->with(['tour', 'guia'])
            ->withCount(['inscripcionesActivas as inscritos'])
            ->get();

        return view('eventos.index', [
            'eventos' => $eventos,
            'tours' => Tour::query()->orderBy('nombre')->get(),
            'estados' => Evento::ESTADOS,
            'estadoSeleccionado' => $estado,
            'tourSeleccionado' => $request->integer('tour_id'),
        ]);
    }

    public function show(Request $request, Evento $evento): View
    {
        $evento->load(['tour', 'guia'])->loadCount(['inscripcionesActivas as inscritos']);

        $inscripcion = $evento->inscripcionDe($request->user());

        return view('eventos.show', [
            'evento' => $evento,
            'ruta' => $evento->rutaOrdenada(),
            'inscripcion' => $inscripcion,
        ]);
    }

    private function estadoValido(string $estado): ?string
    {
        return in_array($estado, Evento::ESTADOS, true) ? $estado : null;
    }
}
