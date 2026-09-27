@extends('layouts.app')

@section('title', 'Mi Resumen')

@section('content')
    <div class="page-header">
        <a href="{{ route('eventos.show', $evento) }}" style="color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            ← Volver al evento
        </a>
        <h1>🏆 Mi Resumen de Actividades</h1>
        <p class="page-description">{{ $evento->tour->nombre }}</p>
    </div>

    <!-- Cards de estadísticas -->
    <div class="grid-3">
        <div class="card stats-card">
            <div class="stats-content">
                <div class="stats-icon">⭐</div>
                <div class="stats-text">
                    <div class="stats-value">{{ $puntajeTotal }}</div>
                    <div class="stats-label">Puntos Total</div>
                </div>
            </div>
        </div>

        <div class="card stats-card">
            <div class="stats-content">
                <div class="stats-icon">🎮</div>
                <div class="stats-text">
                    <div class="stats-value">{{ $participaciones->count() }}</div>
                    <div class="stats-label">Actividades Completadas</div>
                </div>
            </div>
        </div>

        <div class="card stats-card">
            <div class="stats-content">
                <div class="stats-icon">🥇</div>
                <div class="stats-text">
                    <div class="stats-value">#{{ $posicion }}</div>
                    <div class="stats-label">Tu Posición (de {{ $totalTuristas }})</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividades completadas -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h2>Actividades Completadas</h2>
        </div>

        @if ($participaciones->count() > 0)
            <div class="participaciones-list">
                @foreach ($participaciones as $participacion)
                    <div class="participacion-item">
                        <div class="participacion-main">
                            <div class="participacion-header">
                                <h3 class="participacion-title">
                                    @if ($participacion->actividad->tipo === 'trivia')
                                        📝
                                    @elseif ($participacion->actividad->tipo === 'quiz_foto')
                                        📸
                                    @else
                                        🎮
                                    @endif
                                    {{ $participacion->actividad->nombre }}
                                </h3>
                                <span class="participacion-badge">{{ $participacion->puntaje }} / {{ $participacion->actividad->puntos_max }} pts</span>
                            </div>
                            <div class="participacion-details">
                                <span class="detail">📍 {{ $participacion->actividad->parada->nombre }}</span>
                                <span class="detail">📅 {{ $participacion->completado_en->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        <div class="participacion-score">
                            <div class="score-percent">{{ ceil(($participacion->puntaje / $participacion->actividad->puntos_max) * 100) }}%</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">🎮</div>
                <h3>Aún no has completado actividades</h3>
                <p>Completa actividades del tour para ganar puntos y aparecer en el ranking.</p>
            </div>
        @endif
    </div>

    <!-- Botones de acción -->
    <div class="btn-group" style="margin-top: 2rem;">
        <a href="{{ route('fotos.galeria', $evento) }}" class="btn btn-secondary">📸 Ver fotos</a>
        <a href="{{ route('recuerdos.mio', $evento) }}" class="btn btn-secondary">🎁 Mi recuerdo</a>
        <a href="{{ route('eventos.show', $evento) }}" class="btn">Volver</a>
    </div>

    <style>
        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .stats-card {
            padding: 1.5rem;
            text-align: center;
        }

        .stats-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .stats-icon {
            font-size: 2.5rem;
        }

        .stats-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stats-label {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .participaciones-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .participacion-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: var(--bg);
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.3s;
        }

        .participacion-item:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.1);
        }

        .participacion-main {
            flex: 1;
        }

        .participacion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .participacion-title {
            margin: 0;
            font-size: 1.05rem;
            color: var(--text);
        }

        .participacion-badge {
            background: var(--primary);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .participacion-details {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .detail {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .participacion-score {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(139, 69, 19, 0.1) 0%, rgba(160, 82, 45, 0.1) 100%);
            border: 2px solid var(--primary);
            margin-left: 1rem;
        }

        .score-percent {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
        }

        @media (max-width: 768px) {
            .participacion-item {
                flex-direction: column;
            }

            .participacion-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .participacion-score {
                margin-left: 0;
                margin-top: 1rem;
            }
        }
    </style>
@endsection
