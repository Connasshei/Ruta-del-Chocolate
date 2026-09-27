<?php

namespace App\Http\Controllers;

use App\Exceptions\ReservaNoDisponibleException;
use App\Http\Requests\GuardarEventoRequest;
use App\Models\Evento;
use App\Models\Tour;
use App\Services\EventoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventoController extends Controller
{
    public function __construct(private readonly EventoService $eventos)
    {
    }

    public function index(Request $request): View
    {
        $eventos = Evento::query()
            ->visiblesPara($request->user())
            ->when($request->user()->hasRole('guia'), fn ($query) => $query->deGuia($request->user()))
            ->conEstado($request->string('estado')->toString() ?: null)
            ->recientes()
            ->with('tour')
            ->withCount(['inscripciones as inscripciones_totales', 'inscripcionesActivas as inscritos'])
            ->get();

        return view('guias.eventos.index', [
            'eventos' => $eventos,
            'estados' => Evento::ESTADOS,
            'estadoSeleccionado' => $request->string('estado')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('guias.eventos.create', [
            'tours' => $this->toursConParadas(),
        ]);
    }

    public function store(GuardarEventoRequest $request): RedirectResponse
    {
        $evento = $this->eventos->crear(
            $request->user(),
            $request->datosEvento(),
            $request->ruta(),
        );

        return redirect()
            ->route('guias.eventos.show', $evento)
            ->with('success', 'Evento creado correctamente.');
    }

    public function show(Evento $evento): View
    {
        $this->verificarAcceso($evento);

        $evento->load(['tour', 'guia'])->loadCount([
            'inscripciones as inscripciones_totales',
            'inscripcionesActivas as inscritos',
        ]);

        return view('guias.eventos.show', [
            'evento' => $evento,
            'ruta' => $evento->rutaOrdenada(),
            'inscripciones' => $evento->inscripciones()
                ->with('turista')
                ->orderByDesc('fecha_inscripcion')
                ->get(),
        ]);
    }

    public function edit(Evento $evento): View
    {
        $this->verificarAcceso($evento);

        return view('guias.eventos.edit', [
            'evento' => $evento->load('eventoParadas'),
            'tours' => $this->toursConParadas(),
        ]);
    }

    public function update(GuardarEventoRequest $request, Evento $evento): RedirectResponse
    {
        $this->verificarAcceso($evento);

        $this->eventos->actualizar($evento, $request->datosEvento(), $request->ruta());

        return redirect()
            ->route('guias.eventos.show', $evento)
            ->with('success', 'Evento actualizado correctamente.');
    }

    public function cambiarEstado(Request $request, Evento $evento): RedirectResponse
    {
        $this->verificarAcceso($evento);

        $validados = $request->validate([
            'estado' => ['required', 'in:'.implode(',', Evento::ESTADOS)],
        ], [
            'estado.in' => 'El estado solicitado no es válido.',
        ]);

        try {
            $this->eventos->cambiarEstado($evento, $validados['estado']);
        } catch (ReservaNoDisponibleException $exception) {
            return back()->withErrors(['estado' => $exception->getMessage()]);
        }

        return back()->with('success', 'El evento quedó en estado «'.$evento->estado.'».');
    }

    public function destroy(Evento $evento): RedirectResponse
    {
        $this->verificarAcceso($evento);

        if ($evento->estado !== Evento::ESTADO_PROGRAMADO) {
            return back()->withErrors([
                'evento' => 'Solo se pueden eliminar eventos programados sin recorrido iniciado.',
            ]);
        }

        $evento->delete();

        return redirect()
            ->route('guias.eventos.index')
            ->with('success', 'Evento eliminado.');
    }

    private function toursConParadas()
    {
        return Tour::query()
            ->where('activo', true)
            ->with(['paradas' => fn ($query) => $query->orderBy('orden')])
            ->get();
    }

    private function verificarAcceso(Evento $evento): void
    {
        $usuario = Auth::user();

        if ($usuario->hasRole('admin') || ($usuario->hasRole('guia') && $evento->guia_id === $usuario->id)) {
            return;
        }

        abort(403, 'Este evento pertenece a otro guía.');
    }
}
