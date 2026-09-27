<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarParticipacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'puntaje' => ['required', 'integer', 'min:0'],
            'intentos' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'puntaje.required' => 'El puntaje es requerido.',
            'puntaje.integer' => 'El puntaje debe ser un número entero.',
            'puntaje.min' => 'El puntaje no puede ser menor a 0.',
            'intentos.min' => 'Debe haber al menos 1 intento.',
            'intentos.max' => 'No pueden haber más de 10 intentos.',
        ];
    }
}
