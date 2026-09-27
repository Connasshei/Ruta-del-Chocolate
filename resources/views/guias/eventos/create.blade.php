@extends('layouts.app')

@section('title', 'Crear evento')

@section('content')
    <h1>Crear evento</h1>

    <form method="POST" action="{{ route('guias.eventos.store') }}">
        @csrf
        @include('guias.eventos._form', ['boton' => 'Crear evento'])
    </form>
@endsection
