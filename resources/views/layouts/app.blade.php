<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'La Ruta del Chocolate')</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, sans-serif; background: #f5efe6; min-height: 100vh; }
        .navbar { background: #5b3a1e; color: #fff; padding: .9rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar .brand { font-weight: 700; }
        .navbar form { margin: 0; }
        .btn-logout { background: transparent; border: 1px solid rgba(255,255,255,.7); color: #fff; padding: .35rem .8rem; border-radius: 6px; cursor: pointer; font-size: .85rem; }
        .btn-logout:hover { background: rgba(255,255,255,.15); }
        .container { max-width: 720px; margin: 2rem auto; padding: 0 1.5rem; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.08); padding: 2rem; }
        h1 { color: #5b3a1e; font-size: 1.5rem; margin-top: 0; }
        .rol-badge { display: inline-block; background: #7a5230; color: #fff; border-radius: 999px; padding: .25rem .8rem; font-size: .8rem; font-weight: 600; }
        .rol-box { background: #faf3e8; border: 1px solid #eadcc4; border-radius: 10px; padding: 1rem 1.25rem; color: #5b3a1e; }
    </style>
</head>
<body>
    <nav class="navbar">
        <span class="brand">La Ruta del Chocolate</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Cerrar sesión</button>
        </form>
    </nav>
    <main class="container">
        <div class="card">
            @yield('content')
        </div>
    </main>
</body>
</html>