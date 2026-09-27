@extends('layouts.app')

@section('title', 'Recuerdos Generados')

@section('content')
    <div class="galeria-recuerdos">
        <a href="{{ route('guias.eventos.show', $evento) }}" class="btn btn-sec">← Volver</a>

        <h1>🎁 Recuerdos Generados</h1>
        <p class="evento-info">{{ $evento->tour->nombre }} - {{ $evento->fecha->format('d/m/Y') }}</p>

        <div class="recuerdos-stats">
            <div class="stat">
                <div class="stat-number">{{ $recuerdos->count() }}</div>
                <div class="stat-label">Recuerdos generados</div>
            </div>
        </div>

        @if ($recuerdos->count() > 0)
            <div class="recuerdos-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>Turista</th>
                            <th>Fotos</th>
                            <th>Actividades</th>
                            <th>Puntaje</th>
                            <th>Generado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recuerdos as $recuerdo)
                            <tr>
                                <td>{{ $recuerdo->turista->name }}</td>
                                <td>{{ $recuerdo->datos_generacion['fotos_count'] ?? 0 }}</td>
                                <td>{{ $recuerdo->datos_generacion['actividades_completadas'] ?? 0 }}</td>
                                <td>{{ $recuerdo->datos_generacion['puntaje_total'] ?? 0 }} pts</td>
                                <td>{{ $recuerdo->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('recuerdos.ver', [$evento, $recuerdo]) }}"
                                       class="btn-link"
                                       target="_blank">
                                        Ver
                                    </a>
                                    <a href="{{ route('recuerdos.descargar', [$evento, $recuerdo]) }}"
                                       class="btn-link">
                                        Descargar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="acciones-bulk">
                <p>💡 Los recuerdos se generan automáticamente cuando el evento finaliza.</p>
            </div>
        @else
            <div class="alert alert-info">
                <strong>📋 Sin recuerdos aún</strong>
                <p>Los recuerdos se generarán automáticamente cuando el evento pase a estado "finalizado".</p>
            </div>
        @endif
    </div>

    <style>
        .galeria-recuerdos { max-width: 1000px; margin: 2rem auto; }
        .evento-info {
            color: #666;
            font-size: 0.95rem;
            margin: 0.5rem 0 2rem;
        }
        .recuerdos-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .stat {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .recuerdos-tabla {
            margin: 2rem 0;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f5f5f5;
            font-weight: 600;
        }
        .btn-link {
            color: #8B4513;
            text-decoration: none;
            margin-right: 1rem;
            font-weight: 500;
        }
        .btn-link:hover {
            text-decoration: underline;
        }
        .acciones-bulk {
            text-align: center;
            padding: 1.5rem;
            background: #f9f9f9;
            border-radius: 8px;
            margin-top: 2rem;
            color: #666;
        }
        .alert {
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .alert-info {
            background: #D1ECF1;
            border: 1px solid #BEE5EB;
        }
        .alert-info strong {
            color: #0C5460;
        }
        .alert-info p {
            color: #0C5460;
            margin: 0.5rem 0 0;
        }
    </style>
@endsection
