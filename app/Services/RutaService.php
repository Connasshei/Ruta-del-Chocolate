<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\Parada;
use App\Models\RutaTipo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RutaService
{
    /**
     * Crea un evento basado en una ruta predefinida
     *
     * @param array $data Datos del evento
     * @param string $tipoRuta Tipo de ruta ('completa', 'rapida', etc.)
     * @return Evento
     */
    public function crearEventoDesdeTipo(array $data, string $tipoRuta = 'completa'): Evento
    {
        return DB::transaction(function () use ($data, $tipoRuta) {
            // Crear el evento
            $evento = Evento::create([
                'tour_id' => $data['tour_id'],
                'guia_id' => $data['guia_id'],
                'fecha' => $data['fecha'],
                'hora_inicio' => $data['hora_inicio'],
                'cupo_maximo' => $data['cupo_maximo'] ?? 30,
                'estado' => Evento::ESTADO_PROGRAMADO,
                'tipo_ruta' => $tipoRuta,
                'duracion_total_minutos' => $data['duracion_total_minutos'] ?? 180,
            ]);

            // Obtener el tipo de ruta y sus paradas
            $rutaTipo = RutaTipo::where('tour_id', $evento->tour_id)
                ->where('nombre', $this->obtenerNombreRutaTipo($tipoRuta))
                ->first();

            if ($rutaTipo) {
                $this->asignarParadasDesdeRutaTipo($evento, $rutaTipo);
            }

            return $evento;
        });
    }

    /**
     * Crea un evento personalizado con paradas seleccionadas manualmente
     *
     * @param array $data Datos del evento
     * @param array $paradasIds IDs de paradas a incluir en orden
     * @param array $opciones Opciones adicionales (duraciones, actividades, etc.)
     * @return Evento
     */
    public function crearEventoPersonalizado(
        array $data,
        array $paradasIds,
        array $opciones = []
    ): Evento {
        return DB::transaction(function () use ($data, $paradasIds, $opciones) {
            // Crear evento
            $evento = Evento::create([
                'tour_id' => $data['tour_id'],
                'guia_id' => $data['guia_id'],
                'fecha' => $data['fecha'],
                'hora_inicio' => $data['hora_inicio'],
                'cupo_maximo' => $data['cupo_maximo'] ?? 30,
                'estado' => Evento::ESTADO_PROGRAMADO,
                'tipo_ruta' => Evento::TIPO_RUTA_PERSONALIZADA,
                'duracion_total_minutos' => $data['duracion_total_minutos'] ?? 180,
            ]);

            // Asignar paradas manualmente
            $horaInicio = Carbon::createFromFormat('H:i:s', (string) $evento->hora_inicio);
            $tiempoTranscurrido = 0;
            $tiempoTransitoPorDefecto = 15; // 15 minutos entre paradas

            foreach ($paradasIds as $index => $paradasData) {
                $paradasId = is_array($paradasData) ? $paradasData['parada_id'] : $paradasData;
                $duracionMinutos = is_array($paradasData)
                    ? ($paradasData['duracion_minutos'] ?? 45)
                    : ($opciones['duracion_por_parada'] ?? 45);

                // Agregar tiempo de tránsito (excepto para la primera parada)
                if ($index > 0) {
                    $tiempoTranscurrido += $tiempoTransitoPorDefecto;
                }

                $horaEstimada = $horaInicio
                    ->copy()
                    ->addMinutes($tiempoTranscurrido);

                $actividades = is_array($paradasData) && isset($paradasData['actividades_ids'])
                    ? $paradasData['actividades_ids']
                    : null;

                $evento->paradas()->attach($paradasId, [
                    'orden' => $index + 1,
                    'hora_estimada' => $horaEstimada,
                    'duracion_minutos' => $duracionMinutos,
                    'actividades_ids' => $actividades,
                    'notas_guia' => is_array($paradasData)
                        ? ($paradasData['notas_guia'] ?? null)
                        : null,
                    'estado' => 'pendiente',
                ]);

                $tiempoTranscurrido += $duracionMinutos;
            }

            // Actualizar la duración total del evento
            $evento->update([
                'duracion_total_minutos' => $evento->calcularDuracionTotal(),
            ]);

            return $evento->fresh();
        });
    }

    /**
     * Asigna las paradas de una ruta tipo a un evento
     */
    private function asignarParadasDesdeRutaTipo(Evento $evento, RutaTipo $rutaTipo): void
    {
        $horaInicio = Carbon::createFromFormat('H:i:s', (string) $evento->hora_inicio);
        $tiempoTranscurrido = 0;
        $tiempoTransitoPorDefecto = 15; // 15 minutos entre paradas

        foreach ($rutaTipo->paradasOrdenadas() as $rutaTipoParada) {
            // Agregar tiempo de tránsito
            if ($rutaTipoParada->orden > 1) {
                $tiempoTranscurrido += $tiempoTransitoPorDefecto;
            }

            $horaEstimada = $horaInicio
                ->copy()
                ->addMinutes($tiempoTranscurrido);

            $evento->paradas()->attach($rutaTipoParada->parada_id, [
                'orden' => $rutaTipoParada->orden,
                'hora_estimada' => $horaEstimada,
                'duracion_minutos' => $rutaTipoParada->duracion_minutos,
                'actividades_ids' => $rutaTipoParada->actividades_ids,
                'estado' => 'pendiente',
            ]);

            $tiempoTranscurrido += $rutaTipoParada->duracion_minutos;
        }
    }

    /**
     * Actualiza la ruta de un evento existente
     *
     * @param Evento $evento
     * @param array $paradasIds Nuevas paradas en orden
     * @return Evento
     */
    public function actualizarRuta(Evento $evento, array $paradasIds): Evento
    {
        return DB::transaction(function () use ($evento, $paradasIds) {
            // Eliminar paradas actuales
            $evento->paradas()->detach();

            // Asignar nuevas paradas
            $horaInicio = Carbon::createFromFormat('H:i:s', (string) $evento->hora_inicio);
            $tiempoTranscurrido = 0;

            foreach ($paradasIds as $index => $paradasData) {
                $paradasId = is_array($paradasData) ? $paradasData['parada_id'] : $paradasData;
                $duracionMinutos = is_array($paradasData)
                    ? ($paradasData['duracion_minutos'] ?? 45)
                    : 45;

                if ($index > 0) {
                    $tiempoTranscurrido += 15; // Tiempo de tránsito
                }

                $horaEstimada = $horaInicio
                    ->copy()
                    ->addMinutes($tiempoTranscurrido);

                $evento->paradas()->attach($paradasId, [
                    'orden' => $index + 1,
                    'hora_estimada' => $horaEstimada,
                    'duracion_minutos' => $duracionMinutos,
                    'actividades_ids' => is_array($paradasData)
                        ? ($paradasData['actividades_ids'] ?? null)
                        : null,
                    'estado' => 'pendiente',
                ]);

                $tiempoTranscurrido += $duracionMinutos;
            }

            // Actualizar duración total
            $evento->update([
                'duracion_total_minutos' => $evento->calcularDuracionTotal(),
            ]);

            return $evento->fresh();
        });
    }

    /**
     * Obtiene todas las rutas disponibles para un tour
     */
    public function obtenerRutasDisponibles(int $tourId)
    {
        return RutaTipo::where('tour_id', $tourId)
            ->where('activo', true)
            ->with('paradas.parada')
            ->get();
    }

    /**
     * Mapea el tipo de ruta al nombre en base de datos
     */
    private function obtenerNombreRutaTipo(string $tipo): string
    {
        $mapeo = [
            'completa' => 'Ruta Completa',
            'rapida' => 'Ruta Rápida',
            'centro' => 'Ruta Centro',
        ];

        return $mapeo[$tipo] ?? 'Ruta Completa';
    }

    /**
     * Valida que la duración total no exceda 4 horas
     */
    public function validarDuracionTotal(int $duracionMinutos): bool
    {
        return $duracionMinutos <= 240; // 4 horas máximo
    }
}
