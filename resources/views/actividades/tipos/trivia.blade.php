<!-- Trivia Activity -->
<div class="trivia-activity">
    <form id="trivia-form" method="POST" action="{{ route('actividades.participar', [$evento, $actividad]) }}">
        @csrf

        <div class="preguntas">
            @php
                $preguntas = $actividad->configuracion['preguntas'] ?? [];
            @endphp

            @forelse ($preguntas as $index => $pregunta)
                <div class="pregunta-block">
                    <h3>{{ $index + 1 }}. {{ $pregunta['texto'] ?? 'Pregunta sin texto' }}</h3>

                    <div class="opciones">
                        @php
                            $opciones = $pregunta['opciones'] ?? [];
                        @endphp
                        @foreach ($opciones as $opIndex => $opcion)
                            <label class="opcion-radio">
                                <input type="radio"
                                       name="pregunta_{{ $index }}"
                                       value="{{ $opIndex }}"
                                       required>
                                <span>{{ $opcion['texto'] ?? 'Opción sin texto' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="muted">Esta trivia aún no tiene preguntas configuradas.</p>
            @endforelse
        </div>

        <input type="hidden" name="puntaje" id="puntaje" value="0">
        <input type="hidden" name="intentos" value="1">

        <button type="submit" class="btn btn-primary btn-large">Enviar Respuestas</button>
    </form>
</div>

<style>
    .trivia-activity { margin: 2rem 0; }
    .preguntas { margin: 2rem 0; }
    .pregunta-block {
        background: white;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid #8B4513;
    }
    .pregunta-block h3 {
        margin-bottom: 1rem;
        color: #333;
    }
    .opciones {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .opcion-radio {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 4px;
        transition: background 0.2s;
    }
    .opcion-radio:hover {
        background: rgba(139, 69, 19, 0.1);
    }
    .opcion-radio input[type="radio"] {
        cursor: pointer;
    }
    .btn-large {
        padding: 1rem 2rem;
        font-size: 1.05rem;
        width: 100%;
    }
</style>

<script>
    document.getElementById('trivia-form').addEventListener('submit', function(e) {
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
