<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubirFotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imagen' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:5120'], // 5MB
            'parada_id' => ['nullable', 'integer', 'exists:paradas,id'],
            'tipo' => ['required', 'in:actividad,libre'],
        ];
    }

    public function messages(): array
    {
        return [
            'imagen.required' => 'Debes seleccionar una imagen.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'Solo se aceptan imágenes JPG, PNG o WebP.',
            'imagen.max' => 'La imagen no debe superar 5MB.',
            'parada_id.exists' => 'La parada seleccionada no existe.',
            'tipo.in' => 'El tipo de foto no es válido.',
        ];
    }
}
