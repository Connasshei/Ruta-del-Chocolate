<?php

namespace App\Http\Controllers;

use App\Exceptions\ReservaNoDisponibleException;
use App\Models\Evento;
use App\Models\Inscripcion;
use App\Services\InscripcionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    public function __construct(private readonly InscripcionService $inscripciones)
    {
    }

    public function store(Request $request, Evento $evento): RedirectResponse
    {
        try {
            $inscripcion = $this->inscripciones->registrar($request->user(), $evento);
        } catch (ReservaNoDisponibleException $exception) {
            return back()->withErrors(['inscripcion' => $exception->getMessage()]);
        }

        return redirect()
            ->route('eventos.show', $inscripcion->evento)
            ->with('success', 'Reserva confirmada. Te esperamos en la Ruta del Chocolate.');
    }

    public function destroy(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        abort_unless($request->user()->id === $inscripcion->user_id, 403, 'Esa inscripcion no es tuya.');

        try {
            $this->inscripciones->cancelar($inscripcion);
        } catch (ReservaNoDisponibleException $exception) {
            return back()->withErrors(['inscripcion' => $exception->getMessage()]);
        }

        return back()->with('success', 'Tu reserva fue cancelada y el cupo quedo libre.');
    }

    public function actualizarEstado(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $validados = $request->validate([
            'estado' => ['required', 'in:'.implode(',', [Inscripcion::ESTADO_CONFIRMADO, Inscripcion::ESTADO_CANCELADO])],
        ], [
            'estado.in' => 'El estado solicitado no es valido.',
        ]);

        try {
            $inscripcion = $validados['estado'] === Inscripcion::ESTADO_CONFIRMADO
                ? $this->inscripciones->confirmar($inscripcion)
                : $this->inscripciones->cancelar($inscripcion);
        } catch (ReservaNoDisponibleException $exception) {
            return back()->withErrors(['inscripcion' => $exception->getMessage()]);
        }

        return back()->with('success', 'La inscripcion de '.$inscripcion->turista->name.' quedo en estado '.$inscripcion->estado.'.');
    }
}
