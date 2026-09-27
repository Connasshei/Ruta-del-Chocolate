@extends('layouts.app')

@section('title', 'Evento')

@section('content')
    <h1>{{ $evento->tour->nombre }}</h1>

    <p>
        <span class="estado estado-{{ $evento->estado }}">{{ $evento->estado }}</span>
        <span class="muted">&middot; {{ $evento->fecha->format('d/m/Y') }} a las {{ $evento->horaInicio() }}</span>
    </p>

    <p>
        Guia: <strong>{{ $evento->guia->name }}</strong>
        &middot; Inscriptos: <strong>{{ $evento->inscritos }} / {{ $evento->cupo_maximo }}</strong>
    </p>

    <div class="btn-row">
        <a href="{{ route('guias.eventos.edit', $evento) }}" class="btn btn-sec">Editar evento</a>

        @foreach (['en_curso' => 'Iniciar recorrido', 'finalizado' => 'Finalizar evento', 'cancelado' => 'Cancelar evento', 'programado' => 'Reactivar evento'] as $estado => $etiqueta)
            @if (in_array($estado, \App\Models\Evento::TRANSICIONES[$evento->estado] ?? [], true))
                <form method="POST" action="{{ route('guias.eventos.estado', $evento) }}" style="margin:0">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="{{ $estado }}">
                    <button type="submit" class="btn {{ $estado === 'cancelado' ? 'btn-danger' : '' }}">{{ $etiqueta }}</button>
                </form>
            @endif
        @endforeach

        @if ($evento->estado === \App\Models\Evento::ESTADO_PROGRAMADO)
            <form method="POST" action="{{ route('guias.eventos.destroy', $evento) }}" style="margin:0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
        @endif
    </div>

    <h2>Recorrido</h2>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Parada</th>
            <th>Hora estimada</th>
            <th>Estado</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($ruta as $item)
            <tr>
                <td>{{ $item->pivot->orden }}</td>
                <td>{{ $item->nombre }}</td>
                <td>{{ $item->pivot->hora_estimada ? substr($item->pivot->hora_estimada, 0, 5) : '-' }}</td>
                <td><span class="estado estado-{{ $item->pivot->estado }}">{{ $item->pivot->estado }}</span></td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="muted">Este evento todavia no tiene paradas asignadas.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2>Turistas inscritos ({{ $evento->inscritos }}/{{ $evento->cupo_maximo }})</h2>
    <table>
        <thead>
        <tr>
            <th>Turista</th>
            <th>Correo</th>
            <th>Fecha de reserva</th>
            <th>Estado</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($inscripciones as $inscripcion)
            <tr>
                <td>{{ $inscripcion->turista->name }}</td>
                <td>{{ $inscripcion->turista->email }}</td>
                <td>{{ $inscripcion->fecha_inscripcion?->format('d/m/Y H:i') }}</td>
                <td><span class="estado estado-{{ $inscripcion->estado }}">{{ $inscripcion->estado }}</span></td>
                <td>
                    @if ($evento->estado !== \App\Models\Evento::ESTADO_FINALIZADO)
                        <form method="POST" action="{{ route('guias.inscripciones.estado', $inscripcion) }}" style="margin:0">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado"
                                   value="{{ $inscripcion->estado === \App\Models\Inscripcion::ESTADO_CANCELADO
                                       ? \App\Models\Inscripcion::ESTADO_CONFIRMADO
                                       : \App\Models\Inscripcion::ESTADO_CANCELADO }}">
                            <button type="submit" class="btn btn-sec">
                                {{ $inscripcion->estado === \App\Models\Inscripcion::ESTADO_CANCELADO ? 'Reactivar' : 'Cancelar' }}
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="muted">Aun no hay turistas inscritos en este evento.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endsection
