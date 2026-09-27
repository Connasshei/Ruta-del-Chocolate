@extends('layouts.app')

@section('title', 'Galería de Fotos')

@section('content')
    <div class="fotos-container">
        <h1>📸 Galería de Fotos - {{ $evento->tour->nombre }}</h1>

        <div class="fotos-tabs">
            <button class="tab-btn active" onclick="mostrarTab('todas')">Todas las fotos</button>
            <button class="tab-btn" onclick="mostrarTab('mias')">Mis fotos</button>
            <button class="tab-btn" onclick="mostrarTab('subir')">Subir foto</button>
        </div>

        <!-- Tab: Todas las fotos -->
        <div id="tab-todas" class="tab-content active">
            <h2>Todas las fotos del evento</h2>
            <div class="fotos-grid">
                @forelse ($fotos as $foto)
                    <div class="foto-card">
                        <img src="{{ Storage::disk('fotos')->url($foto->ruta_archivo) }}"
                             alt="Foto"
                             class="foto-thumb">
                        <p class="foto-usuario">{{ $foto->turista->name }}</p>
                        <p class="foto-fecha">{{ $foto->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                @empty
                    <p class="muted">Aún no hay fotos en este evento.</p>
                @endforelse
            </div>
        </div>

        <!-- Tab: Mis fotos -->
        <div id="tab-mias" class="tab-content">
            <h2>Mis fotos</h2>
            <div class="fotos-grid">
                @forelse ($misfotos as $foto)
                    <div class="foto-card">
                        <img src="{{ Storage::disk('fotos')->url($foto->ruta_archivo) }}"
                             alt="Mi foto"
                             class="foto-thumb">
                        <div class="foto-acciones">
                            <a href="{{ route('fotos.descargar', [$evento, $foto]) }}" class="btn btn-sm">Descargar</a>
                            <form method="POST" action="{{ route('fotos.eliminar', [$evento, $foto]) }}"
                                  style="display: inline;"
                                  onsubmit="return confirm('¿Eliminar esta foto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="muted">Aún no has subido fotos.</p>
                @endforelse
            </div>
        </div>

        <!-- Tab: Subir foto -->
        <div id="tab-subir" class="tab-content">
            <h2>Subir una nueva foto</h2>
            <form method="POST" action="{{ route('fotos.subir', $evento) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="imagen">Selecciona una imagen *</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*" required>
                    <small>JPG, PNG o WebP. Máximo 5MB.</small>
                    @error('imagen')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="parada_id">Parada (opcional)</label>
                    <select id="parada_id" name="parada_id">
                        <option value="">- Selecciona una parada -</option>
                        @foreach ($evento->paradas()->orderBy('evento_parada.orden')->get() as $parada)
                            <option value="{{ $parada->id }}">{{ $parada->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo de foto *</label>
                    <select id="tipo" name="tipo" required>
                        <option value="libre">Foto libre</option>
                        <option value="actividad">Foto de actividad</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Subir foto</button>
            </form>
        </div>
    </div>

    <style>
        .fotos-container { max-width: 1200px; margin: 2rem auto; }
        .fotos-tabs { display: flex; gap: 1rem; margin: 2rem 0; border-bottom: 1px solid #ddd; }
        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            color: #666;
            font-weight: 500;
        }
        .tab-btn.active {
            border-bottom-color: #8B4513;
            color: #8B4513;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .fotos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .foto-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
        }
        .foto-thumb {
            width: 100%;
            height: 200px;
            object-fit: cover;
            cursor: pointer;
        }
        .foto-usuario, .foto-fecha {
            padding: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        .foto-acciones {
            padding: 0.5rem;
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
    </style>

    <script>
        function mostrarTab(nombre) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            document.getElementById('tab-' + nombre).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
@endsection
