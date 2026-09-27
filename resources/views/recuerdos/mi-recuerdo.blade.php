@extends('layouts.app')

@section('title', 'Mi Recuerdo')

@section('content')
    <div class="page-header">
        <a href="{{ route('eventos.show', $evento) }}" style="color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            ← Volver
        </a>
        <h1>🎁 Tu Recuerdo del Tour</h1>
        <p class="page-description">{{ $evento->tour->nombre }} • {{ $evento->fecha->format('d \d\e F \d\e Y') }}</p>
    </div>

    @if ($recuerdo)
        <div class="grid" style="margin-bottom: 2rem;">
            <div class="card stats-card">
                <div class="stats-icon">📸</div>
                <div class="stats-value">{{ $recuerdo->datos_generacion['fotos_count'] ?? 0 }}</div>
                <div class="stats-label">Fotos Capturadas</div>
            </div>
            <div class="card stats-card">
                <div class="stats-icon">🎮</div>
                <div class="stats-value">{{ $recuerdo->datos_generacion['actividades_completadas'] ?? 0 }}</div>
                <div class="stats-label">Actividades</div>
            </div>
            <div class="card stats-card">
                <div class="stats-icon">⭐</div>
                <div class="stats-value">{{ $recuerdo->datos_generacion['puntaje_total'] ?? 0 }}</div>
                <div class="stats-label">Puntos Totales</div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h2>Vista Previa de tu Recuerdo</h2>
            </div>
            <div class="preview-info">
                <p>📋 Generado: {{ $recuerdo->created_at->locale('es')->format('l d \d\e F \d\e Y \a \l\a\s H:i') }}</p>
            </div>
            <div class="iframe-container">
                <iframe src="{{ $url }}" title="Tu recuerdo"></iframe>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Descargar tu Recuerdo</h2>
            </div>
            <p style="color: var(--text-light); margin-bottom: 1.5rem;">
                Descarga tu recuerdo personalizado que incluye todas tus fotos, actividades completadas y puntuaciones del tour.
            </p>
            <div class="btn-group">
                <a href="{{ route('recuerdos.ver', [$evento, $recuerdo]) }}" class="btn btn-secondary" target="_blank">
                    👁️ Ver en pantalla completa
                </a>
                <a href="{{ route('recuerdos.descargar', [$evento, $recuerdo]) }}" class="btn">
                    ⬇️ Descargar Recuerdo
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <span class="alert-icon">⏳</span>
            <div>
                <strong>Tu recuerdo aún no está disponible</strong>
                <p style="margin-bottom: 0;">Tu recuerdo personalizado se generará automáticamente cuando el guía finalice el tour. Vuelve más tarde.</p>
            </div>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <div class="card-header">
                <h2>¿Qué incluye tu recuerdo?</h2>
            </div>
            <div class="features-list">
                <div class="feature-item">
                    <span class="feature-icon">📸</span>
                    <div>
                        <h4>Tus Fotos</h4>
                        <p>Todas las fotos que capturaste durante el tour</p>
                    </div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🎮</span>
                    <div>
                        <h4>Actividades Completadas</h4>
                        <p>Listado de todas las actividades que completaste</p>
                    </div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">⭐</span>
                    <div>
                        <h4>Tu Puntuación</h4>
                        <p>Resumen detallado de tus puntos por actividad</p>
                    </div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">📋</span>
                    <div>
                        <h4>Detalles del Tour</h4>
                        <p>Información del tour, fecha, guía y más</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .stats-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem 1rem;
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 0.85rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .preview-info {
            padding: 1rem;
            background: var(--bg);
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .preview-info p {
            margin: 0;
        }

        .iframe-container {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            margin: 1.5rem 0;
        }

        .iframe-container iframe {
            width: 100%;
            height: 600px;
            border: none;
            display: block;
        }

        .features-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .feature-item {
            display: flex;
            gap: 1rem;
            padding: 1.5rem;
            background: var(--bg);
            border-radius: 8px;
            border: 1px solid var(--border);
            align-items: flex-start;
        }

        .feature-icon {
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .feature-item h4 {
            margin: 0 0 0.5rem 0;
            color: var(--primary);
        }

        .feature-item p {
            margin: 0;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .iframe-container iframe {
                height: 400px;
            }

            .features-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
