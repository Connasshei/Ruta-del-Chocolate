@php
    $seleccionadas = isset($evento) ? $evento->eventoParadas->keyBy('parada_id') : collect();
    $filas = $tours->flatMap(fn ($tour) => $tour->paradas->map(fn ($parada) => [
        'tour' => $tour,
        'parada' => $parada,
    ]))->values();
@endphp

<div class="field">
    <label for="tour_id">Tour</label>
    <select name="tour_id" id="tour_id" required data-tour-select>
        @foreach ($tours as $tour)
            <option value="{{ $tour->id }}" @selected(($evento->tour_id ?? null) == $tour->id)>
                {{ $tour->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="grid-2">
    <div class="field">
        <label for="fecha">Fecha</label>
        <input type="date" name="fecha" id="fecha" required
               value="{{ isset($evento) ? $evento->fecha->format('Y-m-d') : now()->addWeek()->format('Y-m-d') }}">
    </div>
    <div class="field">
        <label for="hora_inicio">Hora de inicio</label>
        <input type="time" name="hora_inicio" id="hora_inicio" required
               value="{{ isset($evento) ? $evento->horaInicio() : '09:00' }}">
    </div>
    <div class="field">
        <label for="cupo_maximo">Cupo maximo de turistas</label>
        <input type="number" name="cupo_maximo" id="cupo_maximo" min="1" max="200" required
               value="{{ $evento->cupo_maximo ?? 30 }}">
    </div>
</div>

<div class="field">
    <label>Recorrido (paradas del tour)</label>
    <div class="paradas" data-paradas>
        @foreach ($filas as $index => $fila)
            @php $pivote = $seleccionadas->get($fila['parada']->id); @endphp
            <div class="parada" data-tour-id="{{ $fila['tour']->id }}">
                <label style="font-weight:400">
                    <input type="checkbox" name="paradas[{{ $index }}][id]" value="{{ $fila['parada']->id }}"
                           data-parada-check @checked($pivote)>
                    {{ $fila['tour']->nombre }} &middot; {{ $fila['parada']->nombre }}
                </label>
                <input type="number" name="paradas[{{ $index }}][orden]" min="1" placeholder="Orden"
                       value="{{ $pivote->orden ?? '' }}" data-parada-orden>
                <input type="time" name="paradas[{{ $index }}][hora_estimada]"
                       value="{{ $pivote?->hora_estimada ? substr($pivote->hora_estimada, 0, 5) : '' }}"
                       data-parada-hora>
            </div>
        @endforeach
    </div>
    <p class="muted">Marca las paradas que forman la ruta y define el orden y la hora estimada de cada una.</p>
</div>

<div class="btn-row">
    <button type="submit" class="btn">{{ $boton ?? 'Guardar evento' }}</button>
    <a href="{{ route('guias.eventos.index') }}" class="btn btn-sec">Volver</a>
</div>

@push('scripts')
    <script>
        const tourSelect = document.querySelector('[data-tour-select]');
        const filas = document.querySelectorAll('[data-tour-id]');

        function filtrarParadas() {
            filas.forEach(fila => {
                fila.style.display = fila.dataset.tourId === tourSelect.value ? '' : 'none';
            });
        }

        tourSelect?.addEventListener('change', filtrarParadas);
        filtrarParadas();

        document.querySelectorAll('[data-parada-check]').forEach(check => {
            check.addEventListener('change', () => {
                const orden = check.closest('.parada').querySelector('[data-parada-orden]');
                if (check.checked && !orden.value) {
                    orden.value = document.querySelectorAll('[data-parada-check]:checked').length;
                }
            });
        });
    </script>
@endpush
