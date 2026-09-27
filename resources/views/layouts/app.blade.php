<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'La Ruta del Chocolate')</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, sans-serif; background: #f5efe6; min-height: 100vh; }
        .navbar { background: #5b3a1e; color: #fff; padding: .9rem 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .navbar .brand { font-weight: 700; }
        .navbar .links { display: flex; gap: 1rem; flex-wrap: wrap; }
        .navbar .links a { color: #f3e4d0; text-decoration: none; font-size: .9rem; }
        .navbar .links a:hover { text-decoration: underline; }
        .navbar form { margin: 0; }
        .btn-logout { background: transparent; border: 1px solid rgba(255,255,255,.7); color: #fff; padding: .35rem .8rem; border-radius: 6px; cursor: pointer; font-size: .85rem; }
        .btn-logout:hover { background: rgba(255,255,255,.15); }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1.5rem; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.08); padding: 2rem; }
        h1 { color: #5b3a1e; font-size: 1.5rem; margin-top: 0; }
        h2 { color: #5b3a1e; font-size: 1.2rem; }
        .rol-badge { display: inline-block; background: #7a5230; color: #fff; border-radius: 999px; padding: .25rem .8rem; font-size: .8rem; font-weight: 600; }
        .rol-box { background: #faf3e8; border: 1px solid #eadcc4; border-radius: 10px; padding: 1rem 1.25rem; color: #5b3a1e; }
        .alert { border-radius: 10px; padding: .8rem 1rem; margin-bottom: 1rem; }
        .alert-success { background: #e7f5e9; border: 1px solid #a8d8b5; color: #1f5c33; }
        .alert-error { background: #fdecec; border: 1px solid #f0b3b3; color: #8a1f1f; }
        .alert ul { margin: .3rem 0 0; padding-left: 1.2rem; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { text-align: left; padding: .6rem .5rem; border-bottom: 1px solid #eee3d3; font-size: .92rem; }
        th { color: #7a5230; font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; }
        .estado { display: inline-block; border-radius: 999px; padding: .15rem .7rem; font-size: .78rem; font-weight: 600; }
        .estado-programado { background: #e6eefb; color: #23497f; }
        .estado-en_curso { background: #fff2d4; color: #8a5b12; }
        .estado-finalizado { background: #ececec; color: #4a4a4a; }
        .estado-cancelado { background: #fbe3e3; color: #8a1f1f; }
        .estado-confirmado { background: #e7f5e9; color: #1f5c33; }
        .estado-cancelado, .estado-pendiente { background: #f1f1f1; color: #555; }
        form label { display: block; font-size: .85rem; font-weight: 600; color: #5b3a1e; margin-bottom: .25rem; }
        input[type=text], input[type=email], input[type=number], input[type=date], input[type=time], select {
            width: 100%; padding: .5rem .6rem; border: 1px solid #ddcbb2; border-radius: 8px; font-size: .95rem; background: #fffdfa;
        }
        .field { margin-bottom: 1rem; }
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
        .btn { display: inline-block; border: none; border-radius: 8px; padding: .55rem 1.1rem; font-size: .9rem; cursor: pointer; text-decoration: none; background: #5b3a1e; color: #fff; }
        .btn:hover { background: #7a5230; }
        .btn-sec { background: #efe3d2; color: #5b3a1e; }
        .btn-danger { background: #a12d2d; }
        .btn-row { display: flex; gap: .6rem; flex-wrap: wrap; align-items: center; margin: 1rem 0; }
        .paradas { border: 1px solid #eee3d3; border-radius: 10px; padding: 1rem; max-height: 320px; overflow: auto; }
        .parada { display: grid; grid-template-columns: 1fr 90px 110px; gap: .5rem; align-items: center; padding: .35rem 0; }
        .muted { color: #8a7a68; font-size: .85rem; }
    </style>
</head>
<body>
<nav class="navbar">
    <a class="brand" href="{{ route('home') }}">La Ruta del Chocolate</a>
    <div class="links">
        <a href="{{ route('eventos.index') }}">Eventos</a>
        @if (auth()->user()->hasAnyRole(['guia', 'admin']))
            <a href="{{ route('guias.eventos.index') }}">Mis eventos</a>
            <a href="{{ route('guias.eventos.create') }}">Crear evento</a>
        @endif
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">Cerrar sesion</button>
    </form>
</nav>
<main class="container">
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Revisa lo siguiente:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>
@stack('scripts')
</body>
</html>
