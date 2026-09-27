<?php

namespace App\Http\Requests;

use App\Models\Evento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Descarta las filas de paradas que el guia no marco en el formulario.
     */
    protected function prepareForValidation(): void
    {
        $paradas = collect($this->input('paradas', []))
            ->filter(fn ($parada) => ! empty($parada['id']))
            ->values()
            ->all();

        $this->merge(['paradas' => $paradas]);
    }

    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'integer', Rule::exists('tours', 'id')],
            'fecha' => ['required', 'date', $this->esActualizacion() ? 'before_or_equal:2999-12-31' : 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'cupo_maximo' => ['required', 'integer', 'min:'.$this->cupoMinimo(), 'max:200'],
            'tipo_ruta' => ['nullable', Rule::in(Evento::TIPOS_RUTA)],
            'duracion_total_minutos' => ['nullable', 'integer', 'min:60', 'max:240'],
            'paradas' => ['required', 'array', 'min:1'],
            'paradas.*.id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('paradas', 'id')->where(
                    fn ($query) => $query->where('tour_id', $this->input('tour_id'))
                ),
            ],
            'paradas.*.orden' => ['required', 'integer', 'min:1', 'distinct'],
            'paradas.*.hora_estimada' => ['nullable', 'date_format:H:i'],
            'paradas.*.duracion_minutos' => ['nullable', 'integer', 'min:15', 'max:120'],
            'paradas.*.actividades_ids' => ['nullable', 'array'],
            'paradas.*.actividades_ids.*' => ['integer', Rule::exists('actividades', 'id')],
            'paradas.*.notas_guia' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'paradas.required' => 'Selecciona al menos una parada para el recorrido.',
            'paradas.*.id.exists' => 'Alguna de las paradas seleccionadas no pertenece al tour elegido.',
            'paradas.*.id.distinct' => 'No puedes repetir la misma parada en el recorrido.',
            'paradas.*.orden.distinct' => 'Cada parada debe tener un orden distinto.',
            'cupo_maximo.min' => 'El cupo no puede ser menor al número de turistas ya inscritos.',
        ];
    }

    public function datosEvento(): array
    {
        $validados = $this->validated();

        return [
            'tour_id' => (int) $validados['tour_id'],
            'fecha' => $validados['fecha'],
            'hora_inicio' => $validados['hora_inicio'],
            'cupo_maximo' => (int) $validados['cupo_maximo'],
            'tipo_ruta' => $validados['tipo_ruta'] ?? Evento::TIPO_RUTA_PERSONALIZADA,
            'duracion_total_minutos' => (int) ($validados['duracion_total_minutos'] ?? 180),
        ];
    }

    /**
     * Recorrido solicitado por el guía, ya ordenado por su posición en la ruta.
     *
     * @return array<int, array{parada_id: int, orden: int, hora_estimada: ?string, duracion_minutos: int, actividades_ids: ?array, notas_guia: ?string}>
     */
    public function ruta(): array
    {
        return collect($this->validated()['paradas'])
            ->sortBy('orden')
            ->values()
            ->map(fn (array $parada) => [
                'parada_id' => (int) $parada['id'],
                'orden' => (int) $parada['orden'],
                'hora_estimada' => $parada['hora_estimada'] ?? null,
                'duracion_minutos' => (int) ($parada['duracion_minutos'] ?? 45),
                'actividades_ids' => isset($parada['actividades_ids'])
                    ? array_map('intval', (array) $parada['actividades_ids'])
                    : null,
                'notas_guia' => $parada['notas_guia'] ?? null,
            ])
            ->all();
    }

    public function evento(): ?Evento
    {
        $evento = $this->route('evento');

        return $evento instanceof Evento ? $evento : null;
    }

    private function esActualizacion(): bool
    {
        return $this->evento() !== null;
    }

    private function cupoMinimo(): int
    {
        if (! $this->esActualizacion()) {
            return 1;
        }

        return max(1, $this->evento()->inscripcionesActivas()->count());
    }
}
