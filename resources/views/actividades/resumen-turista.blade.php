@extends('layouts.app')

@section('title', 'Mi Resumen de Actividades')

@section('content')
    <div class="resumen-container">
        <a href="{{ route('eventos.show', $evento) }}" class="btn btn-sec">← Volver</a>

        <h1>🎮 Mi Resumen de Actividades</h1>

        <div class="resumen-header">
            <div class="puntaje-card">
                <div class="puntaje-numero">{{ $puntajeTotal }}</div>
                <div class="puntaje-label">Puntos Total</div>
            </div>
            <div class="posicion-card">
                <div class="posicion-numero">{{ $posicion }} de {{ $totalTuristas }}</div>
                <div class="posicion-label">Tu Posición</div>
            </div>
        </div>

        <div class="actividades-completadas">
            <h2>Actividades Completadas</h2>

            @forelse ($participaciones as $participacion)
                <div class="participacion-item">
                    <div class="participacion-info">
                        <h3>{{ $participacion->actividad->nombre }}</h3>
                        <p class="parada-nombre">{{ $participacion->actividad->parada->nombre }}</p>
                        <p class="fecha-participacion">{{ $participacion->completado_en->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="participacion-puntaje">
                        <span class="puntaje-badge">{{ $participacion->puntaje }} / {{ $participacion->actividad->puntos_max }}</span>
                    </div>
                </div>
            @empty
                <p class="muted">Aún no has completado ninguna actividad.</p>
            @endforelse
        </div>

        <div class="acciones">
            <a href="{{ route('actividades.mostrar', $evento) }}" class="btn btn-primary">Ver más actividades</a>
            <a href="{{ route('fotos.galeria', $evento) }}" class="btn btn-secondary">Ver fotos</a>
        </div>
    </div>

    <style>
        .resumen-container { max-width: 800px; margin: 2rem auto; }
        .resumen-header {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .puntaje-card, .posicion-card {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
        }
        .puntaje-numero, .posicion-numero {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .puntaje-label, .posicion-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        .actividades-completadas { margin: 2rem 0; }
        .actividades-completadas h2 {
            color: #8B4513;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #8B4513;
            padding-bottom: 0.5rem;
        }
        .participacion-item {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .participacion-info h3 {
            margin: 0 0 0.5rem;
            color: #333;
        }
        .parada-nombre {
            color: #666;
            font-size: 0.9rem;
            margin: 0.25rem 0;
        }
        .fecha-participacion {
            color: #999;
            font-size: 0.85rem;
            margin: 0.5rem 0 0;
        }
        .participacion-puntaje { text-align: right; }
        .puntaje-badge {
            background: #8B4513;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }
        .acciones {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn { padding: 0.75rem 1.5rem; }
    </style>
@endsection
