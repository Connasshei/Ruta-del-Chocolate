@extends('layouts.app')

@section('title', 'Galería de Fotos')

@section('content')
    <div class="page-header">
        <h1>📸 Galería de Fotos</h1>
        <p class="page-description">{{ $evento->tour->nombre }} • {{ $evento->fecha->format('d/m/Y') }}</p>
    </div>

    <div class="tabs-navigation">
        <button class="tab-link active" data-tab="todas">
            <span class="tab-icon">🖼️</span>
            <span>Todas ({{ $fotos->count() }})</span>
        </button>
        <button class="tab-link" data-tab="mias">
            <span class="tab-icon">📷</span>
            <span>Mis Fotos ({{ $misfotos->count() }})</span>
        </button>
        <button class="tab-link" data-tab="subir">
            <span class="tab-icon">⬆️</span>
            <span>Subir Nueva</span>
        </button>
    </div>

    <!-- Tab: Todas las fotos -->
    <div id="tab-todas" class="tab-pane active">
        @if ($fotos->count() > 0)
            <div class="fotos-grid">
                @foreach ($fotos as $foto)
                    <div class="foto-card">
                        <div class="foto-image-wrapper">
                            <img src="{{ Storage::disk('fotos')->url($foto->ruta_archivo) }}"
                                 alt="Foto"
                                 class="foto-image">
                        </div>
                        <div class="foto-info">
                            <p class="foto-usuario">{{ $foto->turista->name }}</p>
                            <p class="foto-fecha">{{ $foto->created_at->format('d/m/Y') }}</p>
                            <span class="badge badge-primary">{{ $foto->tipo === 'actividad' ? '🎮 Actividad' : '📷 Libre' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📸</div>
                <h3>No hay fotos aún</h3>
                <p>Las fotos de los turistas aparecerán aquí cuando las suban durante el tour.</p>
            </div>
        @endif
    </div>

    <!-- Tab: Mis fotos -->
    <div id="tab-mias" class="tab-pane">
        @if ($misfotos->count() > 0)
            <div class="fotos-grid">
                @foreach ($misfotos as $foto)
                    <div class="foto-card">
                        <div class="foto-image-wrapper">
                            <img src="{{ Storage::disk('fotos')->url($foto->ruta_archivo) }}"
                                 alt="Mi foto"
                                 class="foto-image">
                        </div>
                        <div class="foto-info">
                            <p class="foto-usuario">Tu foto</p>
                            <p class="foto-fecha">{{ $foto->created_at->format('d/m/Y H:i') }}</p>
                            <div class="foto-actions">
                                <a href="{{ route('fotos.descargar', [$evento, $foto]) }}" class="btn btn-sm">Descargar</a>
                                <form method="POST" action="{{ route('fotos.eliminar', [$evento, $foto]) }}"
                                      style="display: inline;"
                                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta foto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📷</div>
                <h3>Aún no has subido fotos</h3>
                <p>Ve a la pestaña "Subir Nueva" para capturar tus momentos del tour.</p>
            </div>
        @endif
    </div>

    <!-- Tab: Subir foto -->
    <div id="tab-subir" class="tab-pane">
        <div class="card">
            <div class="card-header">
                <h2>Subir una Nueva Foto</h2>
            </div>

            <form method="POST" action="{{ route('fotos.subir', $evento) }}" enctype="multipart/form-data">
                @csrf

                <div class="field">
                    <label for="imagen">Selecciona una imagen *</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="imagen" name="imagen" accept="image/*" required>
                        <div class="file-input-label">
                            <span class="file-icon">📁</span>
                            <span class="file-text">Haz clic o arrastra una imagen aquí</span>
                        </div>
                    </div>
                    <small>JPG, PNG o WebP • Máximo 5MB</small>
                    @error('imagen')
                        <div class="alert alert-error" style="margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label for="parada_id">Parada (opcional)</label>
                        <select id="parada_id" name="parada_id">
                            <option value="">— Selecciona una parada —</option>
                            @foreach ($evento->paradas()->orderBy('evento_parada.orden')->get() as $parada)
                                <option value="{{ $parada->id }}">{{ $parada->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="tipo">Tipo de foto *</label>
                        <select id="tipo" name="tipo" required>
                            <option value="libre">📷 Foto libre</option>
                            <option value="actividad">🎮 Foto de actividad</option>
                        </select>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn">Subir Foto</button>
                    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .tabs-navigation {
            display: flex;
            gap: 0.5rem;
            margin: 2rem 0;
            border-bottom: 2px solid var(--border);
            flex-wrap: wrap;
        }

        .tab-link {
            background: none;
            border: none;
            padding: 1rem 1.5rem;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-light);
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab-link:hover {
            color: var(--primary);
        }

        .tab-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-icon {
            font-size: 1.2rem;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fotos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .foto-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .foto-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        .foto-image-wrapper {
            overflow: hidden;
            background: var(--bg);
            height: 180px;
        }

        .foto-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .foto-card:hover .foto-image {
            transform: scale(1.05);
        }

        .foto-info {
            padding: 1rem;
        }

        .foto-usuario {
            margin: 0 0 0.3rem 0;
            font-weight: 600;
            color: var(--text);
            font-size: 0.9rem;
        }

        .foto-fecha {
            margin: 0 0 0.5rem 0;
            color: var(--text-light);
            font-size: 0.8rem;
        }

        .foto-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            background: var(--bg);
            border-radius: 8px;
            margin: 2rem 0;
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--text);
            margin: 1rem 0 0.5rem 0;
        }

        .empty-state p {
            color: var(--text-light);
            margin: 0;
        }

        .file-input-wrapper {
            position: relative;
            margin: 0.75rem 0;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 2rem;
            border: 2px dashed var(--border);
            border-radius: 8px;
            background: var(--bg);
            text-align: center;
            transition: all 0.3s;
        }

        .file-input-wrapper:hover .file-input-label {
            border-color: var(--primary);
            background: rgba(139, 69, 19, 0.05);
        }

        .file-icon {
            font-size: 2rem;
        }

        .file-text {
            color: var(--text);
            font-weight: 500;
        }
    </style>

    <script>
        // Tabs functionality
        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');

                // Hide all tabs
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('active');
                });

                // Remove active from all links
                document.querySelectorAll('.tab-link').forEach(l => {
                    l.classList.remove('active');
                });

                // Show selected tab
                document.getElementById('tab-' + tabName).classList.add('active');
                this.classList.add('active');
            });
        });

        // File input improvement
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                const label = this.parentElement.querySelector('.file-input-label');
                if (label && this.files.length > 0) {
                    label.querySelector('.file-text').textContent = this.files[0].name;
                }
            });
        });
    </script>
@endsection
