@extends('layouts.app')

@section('title', 'Editar evento')

@section('content')
    <h1>Editar evento</h1>
    <p class="muted">Cambia los datos del evento o adjusts la ruta del recorrido.</p>

    <form method="POST" action="{{ route('guias.eventos.update', $evento) }}">
        @csrf
        @method('PUT')
        @include('guias.eventos._form', ['boton' => 'Guardar cambios'])
    </form>
@endsection
