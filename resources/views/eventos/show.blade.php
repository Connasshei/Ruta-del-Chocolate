@extends('layouts.app')

@section('title', 'Detalle del evento')

@section('content')
    <h1>{{ $evento->tour->nombre }}</h1>

    <p>
        <span class="estado estado-{{ $evento->estado }}">{{ $evento->estado }}</span>
        <span class="muted">&middot; {{ $evento->fecha->format('d/m/Y') }} a las {{ $evento->horaInicio() }}</span>
    </p>

    <p>
        Guia: <strong>{{ $evento->guia->name }}</strong>
        &middot; Cupo: <strong>{{ $evento->inscritos }} / {{ $evento->cupo_maximo }}</strong>
        &middot; Lugares libres: <strong>{{ $evento->cuposRestantes() }}</strong>
    </p>

    <h2>Itinerario</h2>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Lugar</th>
            <th>Hora estimada</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($ruta as $item)
            <tr>
                <td>{{ $item->pivot->orden }}</td>
                <td>{{ $item->nombre }}</td>
                <td>{{ $item->pivot->hora_estimada ? substr($item->pivot->hora_estimada, 0, 5) : '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="muted">El recorrido aun no esta definido.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2>Tu reserva</h2>
    @if ($inscripcion && $inscripcion->estado === \App\Models\Inscripcion::ESTADO_CONFIRMADO)
        <p>Ya estas inscrito en este evento desde el {{ $inscripcion->fecha_inscripcion?->format('d/m/Y H:i') }}.</p>
        @if ($evento->estado !== \App\Models\Evento::ESTADO_FINALIZADO)
            <form method="POST" action="{{ route('inscripciones.destroy', $inscripcion) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Cancelar mi reserva</button>
            </form>
        @endif
    @elseif ($evento->estado === \App\Models\Evento::ESTADO_FINALIZADO)
        <p class="muted">Este evento ya se realizo.</p>
    @elseif ($evento->estado === \App\Models\Evento::ESTADO_CANCELADO)
        <p class="muted">Este evento fue cancelado.</p>
    @elseif ($evento->estaLleno())
        <p class="muted">No quedan lugares disponibles para esta fecha.</p>
    @else
        <form method="POST" action="{{ route('inscripciones.store', $evento) }}">
            @csrf
            <button type="submit" class="btn"> inscribirme en este evento</button>
        </form>
    @endif
@endsection
