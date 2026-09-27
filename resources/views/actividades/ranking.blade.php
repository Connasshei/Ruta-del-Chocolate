@extends('layouts.app')

@section('title', 'Ranking en Vivo')

@section('content')
    <div class="page-header">
        <a href="{{ route('guias.eventos.show', $evento) }}" style="color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            ← Volver al evento
        </a>
        <h1>📊 Ranking en Vivo</h1>
        <p class="page-description">{{ $evento->tour->nombre }} • {{ $evento->fecha->format('d/m/Y H:i') }}</p>
    </div>

    <div class="grid">
        <div class="card">
            <div class="stat-item">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <div class="stat-value">{{ $ranking->count() }}</div>
                    <div class="stat-label">Participantes</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="stat-item">
                <div class="stat-icon">🎮</div>
                <div class="stat-info">
                    <div class="stat-value">{{ $participacionesTotales }}</div>
                    <div class="stat-label">Actividades completadas</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h2>Posiciones</h2>
            <div style="font-size: 0.85rem; color: var(--text-light);">
                🔄 Actualiza para ver cambios en tiempo real
            </div>
        </div>

        @if ($ranking->count() > 0)
            <div class="ranking-table">
                <div class="ranking-header">
                    <div class="ranking-col ranking-pos">Pos</div>
                    <div class="ranking-col ranking-name">Turista</div>
                    <div class="ranking-col ranking-activities">Actividades</div>
                    <div class="ranking-col ranking-score">Puntos</div>
                </div>

                @foreach ($ranking as $posicion => $item)
                    <div class="ranking-row @if($posicion === 0) ranking-first @elseif($posicion < 3) ranking-top @endif">
                        <div class="ranking-col ranking-pos">
                            <div class="medal">
                                @if ($posicion === 0)
                                    🥇
                                @elseif ($posicion === 1)
                                    🥈
                                @elseif ($posicion === 2)
                                    🥉
                                @else
                                    {{ $posicion + 1 }}
                                @endif
                            </div>
                        </div>
                        <div class="ranking-col ranking-name">
                            <div class="name">{{ $item->turista->name }}</div>
                        </div>
                        <div class="ranking-col ranking-activities">
                            <span class="badge badge-primary">{{ $item->actividades_completadas }}</span>
                        </div>
                        <div class="ranking-col ranking-score">
                            <span class="score">{{ $item->puntaje_total }} pts</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📊</div>
                <h3>Sin participaciones aún</h3>
                <p>El ranking se actualizará cuando los turistas comiencen a completar actividades.</p>
            </div>
        @endif
    </div>

    <div style="text-align: center; margin-top: 2rem; color: var(--text-light); font-size: 0.9rem;">
        <p>💡 Esta página se actualiza automáticamente cada 30 segundos</p>
    </div>

    <style>
        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            font-size: 2rem;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .ranking-table {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .ranking-header {
            display: grid;
            grid-template-columns: 60px 1fr 140px 120px;
            gap: 1rem;
            background: var(--bg);
            padding: 1rem;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            color: var(--text-light);
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
        }

        .ranking-row {
            display: grid;
            grid-template-columns: 60px 1fr 140px 120px;
            gap: 1rem;
            padding: 1rem;
            align-items: center;
            border-bottom: 1px solid var(--border);
            transition: all 0.3s;
        }

        .ranking-row:hover {
            background: var(--bg);
        }

        .ranking-row.ranking-first {
            background: rgba(16, 185, 129, 0.08);
            border-bottom: 2px solid rgba(16, 185, 129, 0.3);
        }

        .ranking-row.ranking-top {
            background: rgba(139, 69, 19, 0.05);
        }

        .ranking-col {
            display: flex;
            align-items: center;
        }

        .ranking-pos {
            justify-content: center;
        }

        .medal {
            font-size: 1.5rem;
            text-align: center;
            width: 100%;
        }

        .ranking-name {
            font-weight: 600;
            color: var(--text);
        }

        .ranking-activities {
            justify-content: center;
        }

        .ranking-score {
            justify-content: flex-end;
            font-weight: 700;
            color: var(--primary);
            font-size: 1.05rem;
        }

        .score {
            background: rgba(139, 69, 19, 0.1);
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .ranking-header,
            .ranking-row {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .ranking-col {
                justify-content: space-between;
            }

            .ranking-col::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.85rem;
                text-transform: uppercase;
                color: var(--text-light);
                letter-spacing: 0.5px;
            }

            .ranking-pos::before { content: "Pos"; }
            .ranking-name::before { content: "Turista"; }
            .ranking-activities::before { content: "Actividades"; }
            .ranking-score::before { content: "Puntos"; }
        }
    </style>

    <script>
        // Auto-reload cada 30 segundos
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
@endsection
