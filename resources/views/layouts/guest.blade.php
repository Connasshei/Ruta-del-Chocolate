<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'La Ruta del Chocolate')</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, sans-serif; background: #f5efe6; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.08); width: 100%; max-width: 380px; }
        h1 { font-size: 1.25rem; color: #5b3a1e; margin: 0 0 1.25rem; text-align: center; }
        .field { margin-bottom: 1rem; }
        label { display: block; font-size: .85rem; font-weight: 600; color: #5b3a1e; margin-bottom: .35rem; }
        input[type=text], input[type=email], input[type=password] { width: 100%; padding: .6rem .75rem; border: 1px solid #d9c9a8; border-radius: 8px; font-size: .95rem; }
        input:focus { outline: none; border-color: #7a5230; }
        .btn { display: block; width: 100%; padding: .65rem; background: #7a5230; color: #fff; border: 0; border-radius: 8px; font-size: .95rem; cursor: pointer; }
        .btn:hover { background: #5b3a1e; }
        .alert { background: #fdecea; border: 1px solid #f5c6c2; color: #b3261e; padding: .6rem .75rem; border-radius: 8px; font-size: .85rem; margin-bottom: 1rem; }
        .helper { text-align: center; font-size: .85rem; margin-top: 1rem; color: #6b6b6b; }
        .helper a { color: #7a5230; }
        .check { display: flex; gap: .5rem; align-items: flex-start; font-size: .82rem; color: #6b6b6b; }
        .check input { margin-top: .15rem; }
    </style>
</head>
<body>
    <div class="auth-card">
        @yield('content')
    </div>
</body>
</html>