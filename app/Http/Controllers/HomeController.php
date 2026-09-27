<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();

        return view('home', [
            'rol' => $usuario->getRoleNames()->first() ?? 'turista',
            'proximosEventos' => $this->proximosEventos($usuario),
            'resumen' => $this->resumen($usuario),
        ]);
    }

    private function proximosEventos($usuario)
    {
        return Evento::query()
            ->when(
                $usuario->hasRole('guia'),
                fn ($query) => $query->deGuia($usuario),
                fn ($query) => $query->abiertosALasInscripciones()
            )
            ->proximos()
            ->whereDate('fecha', '>=', today())
            ->with('tour')
            ->withCount(['inscripcionesActivas as inscritos'])
            ->limit(3)
            ->get();
    }

    private function resumen($usuario): array
    {
        return Evento::query()
            ->when($usuario->hasRole('guia'), fn ($query) => $query->deGuia($usuario))
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->all();
    }
}
