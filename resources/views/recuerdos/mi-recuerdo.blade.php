@extends('layouts.app')

@section('title', 'Mi Recuerdo')

@section('content')
    <div class="recuerdo-container">
        <a href="{{ route('eventos.show', $evento) }}" class="btn btn-sec">← Volver</a>

        <h1>🎁 Mi Recuerdo de {{ $evento->tour->nombre }}</h1>

        @if ($recuerdo)
            <div class="recuerdo-info">
                <p class="fecha-generacion">
                    Generado: {{ $recuerdo->created_at->locale('es')->format('l d \d\e F \d\e Y') }}
                </p>

                <div class="datos-recuerdo">
                    <div class="dato-card">
                        <div class="dato-numero">{{ $recuerdo->datos_generacion['fotos_count'] ?? 0 }}</div>
                        <div class="dato-label">Fotos capturadas</div>
                    </div>
                    <div class="dato-card">
                        <div class="dato-numero">{{ $recuerdo->datos_generacion['actividades_completadas'] ?? 0 }}</div>
                        <div class="dato-label">Actividades</div>
                    </div>
                    <div class="dato-card">
                        <div class="dato-numero">{{ $recuerdo->datos_generacion['puntaje_total'] ?? 0 }}</div>
                        <div class="dato-label">Puntos totales</div>
                    </div>
                </div>

                <div class="recuerdo-preview">
                    <h2>Vista Previa</h2>
                    <div class="iframe-container">
                        <iframe src="{{ $url }}" style="width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 8px;"></iframe>
                    </div>
                </div>

                <div class="recuerdo-acciones">
                    <a href="{{ route('recuerdos.ver', [$evento, $recuerdo]) }}" class="btn btn-primary" target="_blank">
                        Ver en pantalla completa
                    </a>
                    <a href="{{ route('recuerdos.descargar', [$evento, $recuerdo]) }}" class="btn btn-secondary">
                        ⬇️ Descargar recuerdo
                    </a>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <strong>⏳ Recuerdo no disponible</strong>
                <p>Tu recuerdo será generado automáticamente cuando el evento finalice. Vuelve más tarde.</p>
            </div>
        @endif
    </div>

    <style>
        .recuerdo-container { max-width: 900px; margin: 2rem auto; }
        .fecha-generacion {
            color: #666;
            font-style: italic;
            margin: 1rem 0;
        }
        .datos-recuerdo {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .dato-card {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }
        .dato-numero {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .dato-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .recuerdo-preview {
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 8px;
            margin: 2rem 0;
        }
        .recuerdo-preview h2 {
            color: #8B4513;
            margin-top: 0;
        }
        .iframe-container {
            margin: 1rem 0;
            overflow: hidden;
            border-radius: 8px;
        }
        .recuerdo-acciones {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 4px;
        }
        .alert {
            background: #FFF3CD;
            border: 1px solid #FFE69C;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .alert strong {
            color: #856404;
        }
        .alert p {
            color: #856404;
            margin: 0.5rem 0 0;
        }
    </style>
@endsection
