@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <h1>¡Hola, {{ auth()->user()->name }}!</h1>
    <p>Tu rol es <span class="rol-badge">{{ $rol }}</span></p>

    <div class="rol-box">
        @switch($rol)
            @case('admin')
                Aquí estará el panel de administración del sitio.
                @break
            @case('guia')
                Aquí gestionarás los eventos de la ruta.
                @break
            @default
                Aquí te inscribirás al tour y verás el itinerario.
        @endswitch
    </div>
@endsection