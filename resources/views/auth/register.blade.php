@extends('layouts.guest')

@section('title', 'Registrarse')

@section('content')
    <h1>Registro para turistas</h1>

    @if ($errors->any())
        <div class="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="field">
            <label for="name">Nombre completo</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        </div>
        <div class="field">
            <label for="email">Correo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="field">
            <label for="ciudad_origen">Ciudad de origen (opcional)</label>
            <input id="ciudad_origen" type="text" name="ciudad_origen" value="{{ old('ciudad_origen') }}">
        </div>
        <div class="field">
            <label for="password">Contraseña</label>
            <input id="password" type="password" name="password" required>
        </div>
        <div class="field">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>
        <div class="field check">
            <input id="consentimiento_marketing" type="checkbox" name="consentimiento_marketing" value="1">
            <label for="consentimiento_marketing">Acepto recibir información sobre futuros tours</label>
        </div>
        <button type="submit" class="btn">Registrarse</button>
    </form>

    <p class="helper">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
@endsection