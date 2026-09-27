<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'La Ruta del Chocolate')</title>
    <style>
        * { box-sizing: border-box; }
        :root {
            --primary: #8B4513;
            --secondary: #A0522D;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg: #faf8f3;
            --border: #e5d4c1;
            --text: #2c2416;
            --text-light: #666555;
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        /* Navbar mejorada */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: #fff;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .navbar .brand {
            font-size: 1.3rem;
            font-weight: 700;
            text-decoration: none;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-menu {
            display: flex;
            gap: 2rem;
            flex: 1;
            flex-wrap: wrap;
        }

        .navbar-menu a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s;
            padding: 0.5rem 0;
            border-bottom: 2px solid transparent;
        }

        .navbar-menu a:hover {
            color: #fff;
            border-bottom-color: rgba(255, 255, 255, 0.5);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-size: 0.85rem;
        }

        .user-name {
            font-weight: 600;
            color: #fff;
        }

        .user-role {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.6);
        }

        /* Layout principal */
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            min-height: calc(100vh - 200px);
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: var(--primary);
            font-size: 2rem;
            margin: 0 0 0.5rem 0;
        }

        .page-description {
            color: var(--text-light);
            font-size: 0.95rem;
            margin: 0;
        }

        /* Cards */
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            border: 1px solid var(--border);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border);
        }

        .card-header h2 {
            margin: 0;
            color: var(--primary);
            font-size: 1.3rem;
        }

        /* Alertas mejoradas */
        .alert {
            border-radius: 8px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left-color: var(--success);
            color: #047857;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: var(--danger);
            color: #7f1d1d;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-left-color: var(--warning);
            color: #854d0e;
        }

        .alert-icon {
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .alert ul {
            margin: 0.5rem 0 0 0;
            padding-left: 1.5rem;
        }

        .alert li {
            margin-bottom: 0.3rem;
        }

        /* Tablas */
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--bg);
            color: var(--text);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.95rem;
        }

        tr:hover {
            background: rgba(139, 69, 19, 0.02);
        }

        /* Badges y estados */
        .badge {
            display: inline-block;
            border-radius: 20px;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-primary {
            background: rgba(139, 69, 19, 0.15);
            color: var(--primary);
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.15);
            color: #047857;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #854d0e;
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #7f1d1d;
        }

        .estado {
            display: inline-block;
            border-radius: 6px;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .estado-programado {
            background: rgba(59, 130, 246, 0.15);
            color: #1e40af;
        }

        .estado-en_curso {
            background: rgba(245, 158, 11, 0.15);
            color: #854d0e;
        }

        .estado-finalizado {
            background: rgba(16, 185, 129, 0.15);
            color: #047857;
        }

        .estado-cancelado {
            background: rgba(239, 68, 68, 0.15);
            color: #7f1d1d;
        }

        .estado-confirmada {
            background: rgba(16, 185, 129, 0.15);
            color: #047857;
        }

        /* Formularios */
        .field {
            margin-bottom: 1.5rem;
        }

        .field label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .field small {
            display: block;
            color: var(--text-light);
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }

        input[type=text],
        input[type=email],
        input[type=password],
        input[type=number],
        input[type=date],
        input[type=time],
        input[type=file],
        select,
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.95rem;
            background: #fff;
            color: var(--text);
            font-family: inherit;
            transition: all 0.3s;
        }

        input[type=text]:focus,
        input[type=email]:focus,
        input[type=password]:focus,
        input[type=number]:focus,
        input[type=date]:focus,
        input[type=time]:focus,
        input[type=file]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        /* Botones */
        .btn {
            display: inline-block;
            border: none;
            border-radius: 6px;
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            background: var(--primary);
            color: #fff;
            transition: all 0.3s;
            text-align: center;
        }

        .btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
        }

        .btn-secondary {
            background: var(--border);
            color: var(--text);
        }

        .btn-secondary:hover {
            background: #d4c4b1;
            color: var(--text);
        }

        .btn-danger {
            background: var(--danger);
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin: 1rem 0;
        }

        /* Grid y layout */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        /* Footer */
        footer {
            background: var(--primary);
            color: #fff;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
            font-size: 0.9rem;
        }

        footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Utilities */
        .muted {
            color: var(--text-light);
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 2rem; }

        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .navbar-menu {
                width: 100%;
            }

            main {
                padding: 1rem;
            }

            .card {
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .grid, .grid-2, .grid-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-content">
        <a href="{{ route('home') }}" class="brand">🍫 La Ruta del Chocolate</a>
        <div class="navbar-menu">
            <a href="{{ route('eventos.index') }}">Eventos</a>
            @if (auth()->user()->hasAnyRole(['guia', 'admin']))
                <a href="{{ route('guias.eventos.index') }}">Mis Eventos</a>
                <a href="{{ route('guias.eventos.create') }}">Crear Evento</a>
            @endif
        </div>
        <div class="navbar-user">
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-role">
                    @if (auth()->user()->hasRole('admin')) Admin
                    @elseif (auth()->user()->hasRole('guia')) Guía
                    @else Turista
                    @endif
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">Salir</button>
            </form>
        </div>
    </div>
</nav>

<main>
    @if (session('success'))
        <div class="alert alert-success">
            <span class="alert-icon">✓</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <span class="alert-icon">!</span>
            <div>
                <strong>Por favor revisa:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @yield('content')
</main>

<footer>
    <p>&copy; 2026 La Ruta del Chocolate - Sucre, Bolivia</p>
</footer>

@stack('scripts')
</body>
</html>
