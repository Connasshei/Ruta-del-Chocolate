@extends('layouts.app')

@section('title', 'Ranking en Vivo')

@section('content')
    <div class="ranking-container">
        <a href="{{ route('guias.eventos.show', $evento) }}" class="btn btn-sec">← Volver</a>

        <h1>📊 Ranking en Vivo</h1>
        <p class="subtitle">{{ $evento->tour->nombre }} - {{ $evento->fecha->format('d/m/Y') }}</p>

        <div class="ranking-stats">
            <div class="stat-card">
                <div class="stat-number">{{ $ranking->count() }}</div>
                <div class="stat-label">Turistas participando</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $participacionesTotales }}</div>
                <div class="stat-label">Actividades completadas</div>
            </div>
        </div>

        <div class="ranking-table">
            <table>
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Turista</th>
                        <th>Actividades</th>
                        <th>Puntaje Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ranking as $posicion => $item)
                        <tr class="@if ($posicion === 0) ranking-first @elseif ($posicion < 3) ranking-top @endif">
                            <td class="posicion">
                                @if ($posicion === 0)
                                    🥇
                                @elseif ($posicion === 1)
                                    🥈
                                @elseif ($posicion === 2)
                                    🥉
                                @else
                                    {{ $posicion + 1 }}
                                @endif
                            </td>
                            <td>{{ $item->turista->name }}</td>
                            <td>{{ $item->actividades_completadas }}</td>
                            <td class="puntaje-destaca">{{ $item->puntaje_total }} pts</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted">Aún no hay participaciones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="refresh-hint">
            <p>💡 Actualiza esta página para ver el ranking actualizado en tiempo real</p>
        </div>
    </div>

    <style>
        .ranking-container { max-width: 900px; margin: 2rem auto; }
        .subtitle { color: #666; font-size: 0.95rem; margin: 0.5rem 0 2rem; }
        .ranking-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number { font-size: 2rem; font-weight: bold; }
        .stat-label { font-size: 0.9rem; opacity: 0.9; }
        .ranking-table { margin: 2rem 0; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f5f5f5;
            font-weight: 600;
        }
        tr.ranking-first { background: rgba(255, 215, 0, 0.1); }
        tr.ranking-top { background: rgba(192, 192, 192, 0.1); }
        .posicion {
            font-size: 1.3rem;
            text-align: center;
        }
        .puntaje-destaca {
            font-weight: bold;
            color: #8B4513;
            font-size: 1.1rem;
        }
        .refresh-hint {
            text-align: center;
            padding: 1.5rem;
            background: #f9f9f9;
            border-radius: 8px;
            margin-top: 2rem;
        }
    </style>
@endsection
