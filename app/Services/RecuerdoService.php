<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\Foto;
use App\Models\Participacion;
use App\Models\Recuerdo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecuerdoService
{
    public function __construct(
        private readonly FotoService $fotoService,
        private readonly ActividadService $actividadService
    ) {}

    public function generarRecuerdoEvento(Evento $evento): array
    {
        $turistas = $evento->inscripciones()
            ->where('estado', 'confirmada')
            ->pluck('user_id')
            ->unique();

        $recuerdos = [];

        foreach ($turistas as $turistaId) {
            $turista = User::find($turistaId);
            $recuerdos[] = $this->generarRecuerdoTurista($evento, $turista);
        }

        return $recuerdos;
    }

    public function generarRecuerdoTurista(Evento $evento, User $turista): Recuerdo
    {
        // Obtener datos para el recuerdo
        $fotos = $this->fotoService->obtenerFotosUsuario($evento, $turista);
        $participaciones = $this->actividadService->obtenerParticipacionesTurista($evento, $turista);
        $puntajeTotal = $this->actividadService->obtenerPuntajeTotal($evento, $turista);

        // Generar HTML del recuerdo
        $html = $this->generarHtmlRecuerdo($evento, $turista, $fotos, $participaciones, $puntajeTotal);

        // Convertir a PDF (si no existe dompdf, devolver HTML)
        $ruta = $this->guardarRecuerdo($evento, $turista, $html);

        $datos = [
            'evento_id' => $evento->id,
            'turista_id' => $turista->id,
            'fecha_generacion' => Carbon::now(),
            'fotos_count' => $fotos->count(),
            'actividades_completadas' => $participaciones->count(),
            'puntaje_total' => $puntajeTotal,
        ];

        return Recuerdo::create([
            'evento_id' => $evento->id,
            'user_id' => $turista->id,
            'ruta_archivo' => $ruta,
            'datos_generacion' => $datos,
        ]);
    }

    private function generarHtmlRecuerdo(
        Evento $evento,
        User $turista,
        $fotos,
        $participaciones,
        int $puntajeTotal
    ): string {
        $fotosHtml = $fotos->map(function ($foto) {
            $url = $this->fotoService->obtenerUrlFoto($foto);
            return "<img src='{$url}' style='max-width: 150px; margin: 5px;' />";
        })->join('');

        $actividadesHtml = $participaciones->map(function ($p) {
            return "
            <tr>
                <td>{$p->actividad->nombre}</td>
                <td>{$p->puntaje}/{$p->actividad->puntos_max}</td>
            </tr>";
        })->join('');

        $fecha = Carbon::now()->locale('es')->format('l d \d\e F \d\e Y');

        return "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 3px solid #8B4513; padding-bottom: 20px; }
        .header h1 { color: #8B4513; margin: 0; }
        .content { margin: 30px 0; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #8B4513; border-left: 4px solid #8B4513; padding-left: 10px; }
        .fotos { display: flex; flex-wrap: wrap; gap: 10px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #8B4513; color: white; }
        .puntaje-total { font-size: 24px; color: #8B4513; font-weight: bold; }
        .footer { text-align: center; margin-top: 40px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='header'>
        <h1>🍫 Mi Recuerdo de La Ruta del Chocolate</h1>
        <p>Evento: {$evento->tour->nombre}</p>
        <p>Fecha: {$fecha}</p>
    </div>

    <div class='content'>
        <div class='section'>
            <h2>👋 Hola, {$turista->name}!</h2>
            <p>
                Gracias por participar en nuestro tour. Aquí están tus mejores momentos
                y el puntaje que obtuviste durante la experiencia.
            </p>
        </div>

        <div class='section'>
            <h2>📸 Tus Fotos</h2>
            <div class='fotos'>
                {$fotosHtml}
            </div>
        </div>

        <div class='section'>
            <h2>🎮 Actividades Completadas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Puntaje</th>
                    </tr>
                </thead>
                <tbody>
                    {$actividadesHtml}
                </tbody>
            </table>
        </div>

        <div class='section'>
            <h2>⭐ Puntaje Total</h2>
            <p class='puntaje-total'>{$puntajeTotal} puntos</p>
        </div>

        <div class='section'>
            <h2>📋 Detalles del Tour</h2>
            <ul>
                <li><strong>Tour:</strong> {$evento->tour->nombre}</li>
                <li><strong>Guía:</strong> {$evento->guia->name}</li>
                <li><strong>Fecha:</strong> {$evento->fecha->format('d/m/Y')}</li>
                <li><strong>Duración:</strong> {$evento->duracion_total_minutos} minutos</li>
            </ul>
        </div>
    </div>

    <div class='footer'>
        <p>© La Ruta del Chocolate - Sucre, Bolivia</p>
        <p>Generado: {$fecha}</p>
    </div>
</body>
</html>";
    }

    private function guardarRecuerdo(Evento $evento, User $turista, string $html): string
    {
        $nombreArchivo = "recuerdo_{$evento->id}_{$turista->id}_{time()}.html";
        $ruta = "recuerdos/evento_{$evento->id}";

        Storage::disk('public')->put("{$ruta}/{$nombreArchivo}", $html);

        return "{$ruta}/{$nombreArchivo}";
    }

    public function obtenerRecuerdo(Evento $evento, User $turista): ?Recuerdo
    {
        return Recuerdo::where('evento_id', $evento->id)
            ->where('user_id', $turista->id)
            ->first();
    }

    public function obtenerUrlRecuerdo(Recuerdo $recuerdo): string
    {
        return Storage::disk('public')->url($recuerdo->ruta_archivo);
    }

    public function obtenerRecuerdosEvento(Evento $evento)
    {
        return Recuerdo::where('evento_id', $evento->id)
            ->with('turista')
            ->orderByDesc('created_at')
            ->get();
    }

    public function descargarRecuerdo(Recuerdo $recuerdo)
    {
        $url = $this->obtenerUrlRecuerdo($recuerdo);
        return $url; // Frontend maneja la descarga
    }
}
