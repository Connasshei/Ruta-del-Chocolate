@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <h1>¡Hola, {{ auth()->user()->name }}!</h1>
    <p>Tu rol es <span class="rol-badge">{{ $rol }}</span></p>

    <div class="rol-box">
        @switch($rol)
            @case('admin')
                Aqui estara el panel de administracion del sitio.
                @break
            @case('guia')
                Aqui gestionaras los eventos de la ruta.
                @break
            @default
                Aqui te inscribiras al tour y veras el itinerario.
        @endswitch
    </div>

    <h2>Resumen de eventos</h2>
    <p class="muted">
        Programados: {{ $resumen['programado'] ?? 0 }}
        &middot; En curso: {{ $resumen['en_curso'] ?? 0 }}
        &middot; Finalizados: {{ $resumen['finalizado'] ?? 0 }}
        &middot; Cancelados: {{ $resumen['cancelado'] ?? 0 }}
    </p>

    <h2>{{ $rol === 'guia' ? 'Mis proximos eventos' : 'Proximos eventos' }}</h2>
    <table>
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Tour</th>
            <th>Estado</th>
            <th>Cupo</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($proximosEventos as $evento)
            <tr>
                <td>{{ $evento->fecha->format('d/m/Y') }} {{ $evento->horaInicio() }}</td>
                <td>{{ $evento->tour->nombre }}</td>
                <td><span class="estado estado-{{ $evento->estado }}">{{ $evento->estado }}</span></td>
                <td>{{ $evento->cupo_maximo - $evento->inscritos }} libres</td>
                <td>
                    <a href="{{ route('eventos.show', $evento) }}">Ver</a>
                    @if (in_array($rol, ['guia', 'admin'], true))
                        &middot; <a href="{{ route('guias.eventos.show', $evento) }}">Gestionar</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="muted">No hay eventos programados por ahora.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="btn-row">
        <a href="{{ route('eventos.index') }}" class="btn">Ver todos los eventos</a>
        @if (in_array($rol, ['guia', 'admin'], true))
            <a href="{{ route('guias.eventos.create') }}" class="btn btn-sec">Crear evento</a>
        @endif
    </div>
@endsection
