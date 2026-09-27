@extends('layouts.app')

@section('title', 'Actividad: ' . $actividad->nombre)

@section('content')
    <div class="actividad-container">
        <a href="{{ route('eventos.show', $evento) }}" class="btn btn-sec">← Volver</a>

        <div class="actividad-header">
            <h1>🎮 {{ $actividad->nombre }}</h1>
            <p class="actividad-parada">{{ $actividad->parada->nombre }}</p>
        </div>

        @if ($yaCompletada)
            <div class="alert alert-success">
                <strong>✓ Ya completaste esta actividad</strong>
                <p>Puntaje obtenido: <strong>{{ $yaCompletada->puntaje }} / {{ $actividad->puntos_max }}</strong></p>
            </div>
        @else
            <div class="actividad-contenido">
                <p class="actividad-descripcion">{{ $actividad->configuracion['descripcion'] ?? 'Completa esta actividad para ganar puntos.' }}</p>

                <div class="actividad-puntos">
                    <strong>Puntos máximos:</strong> {{ $actividad->puntos_max }}
                </div>

                <!-- Aquí va el tipo de actividad específico -->
                @switch($actividad->tipo)
                    @case('trivia')
                        @include('actividades.tipos.trivia', ['actividad' => $actividad, 'evento' => $evento])
                        @break

                    @case('quiz_foto')
                        @include('actividades.tipos.quiz-foto', ['actividad' => $actividad, 'evento' => $evento])
                        @break

                    @default
                        <p class="muted">Tipo de actividad no soportado.</p>
                @endswitch
            </div>
        @endif
    </div>

    <style>
        .actividad-container { max-width: 800px; margin: 2rem auto; }
        .actividad-header { text-align: center; margin: 2rem 0; }
        .actividad-parada { color: #666; font-size: 0.95rem; }
        .actividad-contenido { background: #f9f9f9; padding: 2rem; border-radius: 8px; }
        .actividad-descripcion { font-size: 1.05rem; margin: 1.5rem 0; line-height: 1.8; }
        .actividad-puntos {
            background: white;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
            border-left: 4px solid #8B4513;
        }
    </style>
@endsection
