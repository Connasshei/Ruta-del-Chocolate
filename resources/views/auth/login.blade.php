@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <h1>La Ruta del Chocolate</h1>

    @if ($errors->any())
        <div class="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="field">
            <label for="email">Correo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="field">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Entrar</button>
    </form>

    <p class="helper">¿Aún no tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></p>
@endsection