<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Recuerdo;
use App\Services\RecuerdoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RecuerdoController extends Controller
{
    public function __construct(private readonly RecuerdoService $recuerdoService)
    {
    }

    public function miRecuerdo(Evento $evento): View|RedirectResponse
    {
        // Solo turistas inscritos
        if (! $evento->estaInscrito(Auth::user())) {
            abort(403, 'No estás inscrito en este evento.');
        }

        $recuerdo = $this->recuerdoService->obtenerRecuerdo($evento, Auth::user());

        if (! $recuerdo) {
            return back()->withErrors(['recuerdo' => 'Tu recuerdo aún no ha sido generado. El evento debe estar finalizado.']);
        }

        return view('recuerdos.mi-recuerdo', [
            'evento' => $evento,
            'recuerdo' => $recuerdo,
            'url' => $this->recuerdoService->obtenerUrlRecuerdo($recuerdo),
        ]);
    }

    public function verRecuerdo(Evento $evento, Recuerdo $recuerdo)
    {
        // Validar que es del evento correcto
        if ($recuerdo->evento_id !== $evento->id) {
            abort(404);
        }

        // Solo el propietario, guía o admin
        if (Auth::id() !== $recuerdo->user_id &&
            $evento->guia_id !== Auth::id() &&
            ! Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $url = $this->recuerdoService->obtenerUrlRecuerdo($recuerdo);
        $contenido = Storage::disk('public')->get($recuerdo->ruta_archivo);

        return response($contenido)
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function descargarRecuerdo(Evento $evento, Recuerdo $recuerdo)
    {
        // Mismo control de acceso
        if ($recuerdo->evento_id !== $evento->id) {
            abort(404);
        }

        if (Auth::id() !== $recuerdo->user_id &&
            $evento->guia_id !== Auth::id() &&
            ! Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $contenido = Storage::disk('public')->get($recuerdo->ruta_archivo);
        $nombreArchivo = "recuerdo_{$evento->id}_{$recuerdo->turista->name}_{$recuerdo->created_at->format('Y-m-d')}.html";

        return response($contenido)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=\"{$nombreArchivo}\"");
    }

    public function galeriaRecuerdos(Evento $evento): View
    {
        // Solo guía o admin
        if ($evento->guia_id !== Auth::id() && ! Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $recuerdos = $this->recuerdoService->obtenerRecuerdosEvento($evento);

        return view('recuerdos.galeria', [
            'evento' => $evento,
            'recuerdos' => $recuerdos,
        ]);
    }
}
