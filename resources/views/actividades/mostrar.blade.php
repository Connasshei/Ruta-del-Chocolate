@extends('layouts.app')

@section('title', 'Actividad: ' . $actividad->nombre)

@section('content')
    <div class="page-header">
        <a href="{{ route('eventos.show', $evento) }}" style="color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            ← Volver al evento
        </a>
        <h1>🎮 {{ $actividad->nombre }}</h1>
        <p class="page-description">{{ $actividad->parada->nombre }} • Gana hasta {{ $actividad->puntos_max }} puntos</p>
    </div>

    @if ($yaCompletada)
        <div class="alert alert-success">
            <span class="alert-icon">✓</span>
            <div>
                <strong>¡Ya completaste esta actividad!</strong>
                <br>Puntaje obtenido: <strong>{{ $yaCompletada->puntaje }} / {{ $actividad->puntos_max }} puntos</strong>
            </div>
        </div>

        <div class="card">
            <div class="completed-info">
                <div class="completed-icon">✓</div>
                <h2 style="margin-top: 0;">Actividad Completada</h2>
                <p>Gracias por participar en esta actividad. Ve a otras actividades para ganar más puntos.</p>
                <a href="{{ route('actividades.resumen', $evento) }}" class="btn">Ver mi resumen</a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="activity-instructions">
                <h2 style="margin-top: 0;">{{ $actividad->configuracion['descripcion'] ?? 'Completa esta actividad para ganar puntos.' }}</h2>

                <div class="activity-info">
                    <div class="info-item">
                        <span class="info-label">Puntos máximos</span>
                        <span class="info-value">{{ $actividad->puntos_max }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tipo</span>
                        <span class="info-value">
                            @if ($actividad->tipo === 'trivia')
                                📝 Trivia
                            @elseif ($actividad->tipo === 'quiz_foto')
                                📸 Quiz Foto
                            @else
                                {{ ucfirst($actividad->tipo) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top: 2rem;">
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

    <style>
        .page-header a {
            transition: all 0.3s;
        }

        .page-header a:hover {
            color: var(--secondary);
            gap: 0.75rem;
        }

        .activity-instructions h2 {
            color: var(--text);
        }

        .activity-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
            padding: 1.5rem;
            background: var(--bg);
            border-radius: 8px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-label {
            font-size: 0.8rem;
            color: var(--text-light);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .completed-info {
            text-align: center;
            padding: 3rem 2rem;
        }

        .completed-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--success);
        }

        .completed-info h2 {
            color: var(--success);
            margin: 1rem 0;
        }

        .completed-info p {
            color: var(--text-light);
            margin: 1rem 0 1.5rem 0;
        }
    </style>
@endsection
