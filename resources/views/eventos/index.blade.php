@extends('layouts.app')

@section('title', 'Eventos disponibles')

@section('content')
    <h1>Eventos disponibles</h1>

    <form method="GET" action="{{ route('eventos.index') }}" class="btn-row">
        <label for="estado" style="margin:0">Estado</label>
        <select name="estado" id="estado" style="width:auto">
            <option value="">Abiertos</option>
            @foreach ($estados as $estado)
                <option value="{{ $estado }}" @selected($estadoSeleccionado === $estado)>{{ $estado }}</option>
            @endforeach
        </select>

        <label for="tour_id" style="margin:0">Tour</label>
        <select name="tour_id" id="tour_id" style="width:auto">
            <option value="">Todos</option>
            @foreach ($tours as $tour)
                <option value="{{ $tour->id }}" @selected($tourSeleccionado === $tour->id)>{{ $tour->nombre }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-sec">Filtrar</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Tour</th>
            <th>Guia</th>
            <th>Estado</th>
            <th>Cupo</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($eventos as $evento)
            <tr>
                <td>{{ $evento->fecha->format('d/m/Y') }} {{ $evento->horaInicio() }}</td>
                <td>{{ $evento->tour->nombre }}</td>
                <td>{{ $evento->guia->name }}</td>
                <td><span class="estado estado-{{ $evento->estado }}">{{ $evento->estado }}</span></td>
                <td>{{ $evento->cupo_maximo - $evento->inscritos }} libres</td>
                <td><a href="{{ route('eventos.show', $evento) }}">Ver detalle</a></td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="muted">No hay eventos con ese filtro por ahora.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endsection
