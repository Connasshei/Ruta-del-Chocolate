<?php

namespace App\Http\Requests;

use App\Models\Evento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo guías y admins pueden crear eventos
        return auth()->user()->hasAnyRole(['guia', 'admin']);
    }

    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'exists:tours,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'cupo_maximo' => ['required', 'integer', 'min:1', 'max:100'],
            'tipo_ruta' => ['required', Rule::in(Evento::TIPOS_RUTA)],
            'duracion_total_minutos' => ['required', 'integer', 'min:60', 'max:240'],

            // Para rutas predefinidas
            'usar_ruta_tipo' => ['sometimes', 'boolean'],

            // Para rutas personalizadas
            'paradas' => ['required_unless:usar_ruta_tipo,true', 'array', 'min:1'],
            'paradas.*.parada_id' => ['required', 'exists:paradas,id'],
            'paradas.*.duracion_minutos' => ['integer', 'min:15', 'max:120'],
            'paradas.*.actividades_ids' => ['array'],
            'paradas.*.actividades_ids.*' => ['exists:actividades,id'],
            'paradas.*.notas_guia' => ['string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required' => 'El tour es requerido',
            'tour_id.exists' => 'El tour seleccionado no existe',
            'fecha.required' => 'La fecha es requerida',
            'fecha.after_or_equal' => 'La fecha no puede ser en el pasado',
            'hora_inicio.required' => 'La hora es requerida',
            'cupo_maximo.required' => 'El cupo máximo es requerido',
            'cupo_maximo.min' => 'El cupo debe ser al menos 1',
            'tipo_ruta.required' => 'Debe seleccionar un tipo de ruta',
            'tipo_ruta.in' => 'El tipo de ruta seleccionado no es válido',
            'duracion_total_minutos.required' => 'La duración es requerida',
            'duracion_total_minutos.max' => 'La duración máxima permitida es 4 horas (240 minutos)',
            'paradas.required_unless' => 'Debe seleccionar al menos una parada para rutas personalizadas',
        ];
    }
}
