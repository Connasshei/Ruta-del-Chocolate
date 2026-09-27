<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubirFotoRequest;
use App\Models\Evento;
use App\Models\Foto;
use App\Services\FotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FotoController extends Controller
{
    public function __construct(private readonly FotoService $fotoService)
    {
    }

    public function galeria(Evento $evento): View
    {
        // Solo turistas inscritos pueden ver fotos
        if (! $evento->estaInscrito(Auth::user())) {
            abort(403, 'No estás inscrito en este evento.');
        }

        $fotos = $this->fotoService->obtenerFotosEvento($evento);
        $misfotos = $this->fotoService->obtenerFotosUsuario($evento, Auth::user());

        return view('fotos.galeria', [
            'evento' => $evento,
            'fotos' => $fotos,
            'misfotos' => $misfotos,
        ]);
    }

    public function subir(SubirFotoRequest $request, Evento $evento): RedirectResponse
    {
        // Validar que turista está inscrito
        if (! $evento->estaInscrito(Auth::user())) {
            return back()->withErrors(['evento' => 'No estás inscrito en este evento.']);
        }

        try {
            $this->fotoService->subirFoto(
                $evento,
                Auth::user(),
                $request->file('imagen'),
                $request->integer('parada_id') ?: null,
                $request->string('tipo')->default(Foto::TIPO_LIBRE)
            );

            return back()->with('success', '¡Foto subida correctamente!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['imagen' => $e->getMessage()]);
        }
    }

    public function eliminar(Evento $evento, Foto $foto): RedirectResponse
    {
        // Solo el propietario o admin puede eliminar
        if (Auth::id() !== $foto->user_id && ! Auth::user()->hasRole('admin')) {
            abort(403, 'No puedes eliminar esta foto.');
        }

        $this->fotoService->eliminarFoto($foto);

        return back()->with('success', 'Foto eliminada.');
    }

    public function descargar(Evento $evento, Foto $foto)
    {
        // Solo turistas del evento o admin
        if (! $evento->estaInscrito(Auth::user()) && ! Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $ruta = $foto->ruta_archivo;
        $disco = 'fotos';

        return response()->download(
            storage_path("app/{$disco}/{$ruta}"),
            "foto_{$foto->id}.{$foto->extension ?? 'jpg'}"
        );
    }
}
