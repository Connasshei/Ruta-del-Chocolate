<!-- Quiz Foto Activity -->
<div class="quiz-foto-activity">
    <form id="quiz-form" method="POST" action="{{ route('actividades.participar', [$evento, $actividad]) }}">
        @csrf

        @php
            $preguntas = $actividad->configuracion['preguntas'] ?? [];
        @endphp

        @forelse ($preguntas as $index => $pregunta)
            <div class="pregunta-foto-block">
                <h3>{{ $index + 1 }}. {{ $pregunta['texto'] ?? 'Pregunta sin texto' }}</h3>

                <div class="fotos-opciones">
                    @php
                        $opciones = $pregunta['opciones'] ?? [];
                    @endphp
                    @foreach ($opciones as $opIndex => $opcion)
                        <label class="foto-opcion">
                            <input type="radio"
                                   name="pregunta_{{ $index }}"
                                   value="{{ $opIndex }}"
                                   required>
                            <img src="{{ $opcion['url'] ?? '' }}"
                                 alt="Opción {{ $opIndex }}"
                                 class="foto-option-img">
                            <span>{{ $opcion['texto'] ?? 'Opción' }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="muted">Este quiz aún no tiene preguntas configuradas.</p>
        @endforelse

        <input type="hidden" name="puntaje" id="puntaje" value="0">
        <input type="hidden" name="intentos" value="1">

        <button type="submit" class="btn btn-primary btn-large">Enviar Respuestas</button>
    </form>
</div>

<style>
    .quiz-foto-activity { margin: 2rem 0; }
    .pregunta-foto-block {
        background: white;
        padding: 2rem;
        margin-bottom: 2rem;
        border-radius: 8px;
        border-left: 4px solid #8B4513;
    }
    .pregunta-foto-block h3 {
        margin-bottom: 1.5rem;
        color: #333;
    }
    .fotos-opciones {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }
    .foto-opcion {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 1rem;
        border-radius: 8px;
        border: 2px solid #ddd;
        transition: all 0.3s;
    }
    .foto-opcion:hover {
        border-color: #8B4513;
        background: rgba(139, 69, 19, 0.05);
    }
    .foto-opcion input[type="radio"] {
        cursor: pointer;
    }
    .foto-opcion input[type="radio"]:checked ~ .foto-option-img {
        border: 3px solid #8B4513;
    }
    .foto-option-img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 4px;
        border: 2px solid transparent;
        transition: border 0.3s;
    }
    .btn-large {
        padding: 1rem 2rem;
        font-size: 1.05rem;
        width: 100%;
    }
</style>

<script>
    document.getElementById('quiz-form').addEventListener('submit', function(e) {
        e.preventDefault();

        @php
            $respuestasCorrectas = [];
            foreach ($preguntas as $index => $pregunta) {
                $respuestasCorrectas[] = $pregunta['respuesta_correcta'] ?? 0;
            }
        @endphp

        const respuestas = @json($respuestasCorrectas);
        let aciertos = 0;

        respuestas.forEach((respuestaCorrecta, index) => {
            const respuestaUsuario = document.querySelector(`input[name="pregunta_${index}"]:checked`);
            if (respuestaUsuario && parseInt(respuestaUsuario.value) === respuestaCorrecta) {
                aciertos++;
            }
        });

        const puntaje = Math.round((aciertos / respuestas.length) * {{ $actividad->puntos_max }});
        document.getElementById('puntaje').value = puntaje;

        this.submit();
    });
</script>
