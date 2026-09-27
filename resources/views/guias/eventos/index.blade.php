@extends('layouts.app')

@section('title', 'Mis eventos')

@section('content')
    <div class="btn-row" style="justify-content: space-between">
        <h1>Eventos del tour</h1>
        <a href="{{ route('guias.eventos.create') }}" class="btn">Crear evento</a>
    </div>

    <form method="GET" action="{{ route('guias.eventos.index') }}" class="btn-row">
        <label for="estado" style="margin:0">Estado</label>
        <select name="estado" id="estado" style="width:auto">
            <option value="">Todos</option>
            @foreach ($estados as $estado)
                <option value="{{ $estado }}" @selected($estadoSeleccionado === $estado)>{{ $estado }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-sec">Filtrar</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Tour</th>
            <th>Estado</th>
            <th>Inscriptos</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($eventos as $evento)
            <tr>
                <td>{{ $evento->fecha->format('d/m/Y') }} {{ $evento->horaInicio() }}</td>
                <td>{{ $evento->tour->nombre }}</td>
                <td><span class="estado estado-{{ $evento->estado }}">{{ $evento->estado }}</span></td>
                <td>{{ $evento->inscritos }} / {{ $evento->cupo_maximo }}</td>
                <td>
                    <a href="{{ route('guias.eventos.show', $evento) }}">Ver</a>
                    &middot;
                    <a href="{{ route('guias.eventos.edit', $evento) }}">Editar</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="muted">Todavia no creaste ningun evento.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endsection
