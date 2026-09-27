# Frontend & Vistas - La Ruta del Chocolate

## 📁 Estructura Actual de Vistas

```
resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php          # Layout principal (header, nav, footer)
    │   └── guest.blade.php        # Layout para login/register
    │
    ├── auth/
    │   ├── login.blade.php        # Formulario login
    │   └── register.blade.php     # Formulario registro (turistas)
    │
    ├── guias/
    │   └── eventos/
    │       ├── index.blade.php    # Listado de eventos (guía)
    │       ├── create.blade.php   # Crear evento
    │       ├── edit.blade.php     # Editar evento
    │       ├── show.blade.php     # Ver detalles evento
    │       └── _form.blade.php    # Formulario compartido (create/edit)
    │
    ├── eventos/
    │   ├── index.blade.php        # Listado eventos públicos (turista)
    │   └── show.blade.php         # Detalles evento + inscripción
    │
    ├── home.blade.php             # Dashboard principal (role-specific)
    └── welcome.blade.php          # Landing (no usada actualmente)
```

---

## 🎯 Vistas Actuales Detalladas

### `layouts/app.blade.php`
**Propósito:** Layout principal compartido por todas las vistas autenticadas

**Incluye:**
- Navbar con usuario y rol
- Logout button
- Breadcrumbs (opcional)
- Flash messages (success/error)

**Mejoras propuestas:**
- Sistema de notificaciones más robusto
- Sidebar para navegación
- Responsive mobile-first

---

### `layouts/guest.blade.php`
**Propósito:** Layout para páginas sin autenticación

**Características:**
- Sin navbar/sidebar
- Centrado
- Links a login/register

---

### `auth/login.blade.php`
**Propósito:** Formulario de login

**Campos:**
```blade
<form method="POST" action="/login">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Ingresar</button>
</form>
```

**Flujo:**
1. POST /login
2. Si es válido → redirige a /home
3. Si falla → vuelve con errores

---

### `auth/register.blade.php`
**Propósito:** Registro de nuevos turistas

**Campos:**
```blade
<input type="text" name="name" required>
<input type="email" name="email" required>
<input type="password" name="password" required>
<input type="password" name="password_confirmation" required>
```

**Nota:** Solo turistas pueden auto-registrarse. Guías/admins deben ser creados por admin.

---

### `home.blade.php`
**Propósito:** Dashboard con rol específico

**Estructura:**
```blade
@if($rol === 'turista')
    - Próximos eventos donde estoy inscrito
    - Botón "Ver todos eventos"
    
@elseif($rol === 'guia')
    - Mis próximos eventos
    - Botón "Crear evento"
    - Resumen de inscritos por evento
    
@elseif($rol === 'admin')
    - Resumen general del sistema
    - Estadísticas
```

**Datos disponibles:**
```php
$rol            // string: 'turista', 'guia', 'admin'
$resumen        // array: ['programado' => 5, 'en_curso' => 2, ...]
$proximosEventos // Collection de Evento
```

---

### `eventos/index.blade.php`
**Propósito:** Listado de eventos disponibles para turistas

**Características:**
- Filtro por estado
- Filtro por tour
- Tabla con: fecha, tour, guía, estado, cupos disponibles
- Link "Ver detalle" para inscribirse

**Query usado:**
```php
Evento::visiblesPara($user)
    ->conEstado($estado)
    ->with('tour', 'guia')
    ->withCount(['inscripciones as inscritos'])
    ->get();
```

---

### `eventos/show.blade.php`
**Propósito:** Detalles de un evento + inscripción

**Información mostrada:**
- Datos del evento (fecha, hora, duración, cupos)
- Nombre y foto del guía
- Itinerario completo (paradas, horas, duraciones)
- Actividades por parada
- Botón "Inscribirse" (si aplica)

**Lógica:**
```blade
@if($evento->admiteInscripciones())
    <form method="POST" action="/eventos/{{ $evento->id }}/inscripciones">
        <button>Inscribirse</button>
    </form>
@else
    <p class="alert">Evento lleno o no disponible</p>
@endif
```

---

### `guias/eventos/index.blade.php`
**Propósito:** Listado de eventos del guía

**Características:**
- Filtro por estado
- Tabla con eventos
- Acciones: Ver, Editar, Eliminar (solo si está en estado 'programado')
- Botón "Crear evento"

---

### `guias/eventos/create.blade.php` y `edit.blade.php`
**Propósito:** Crear/editar evento

**Incluye:**
- Componente `_form.blade.php`
- Carga dinámica de paradas según tour
- Selector de ruta predefinida (opcional)
- Tabla de paradas con formulario inline

**Campos principales:**
```
tour_id (select)
fecha (date picker)
hora_inicio (time)
cupo_maximo (number)
tipo_ruta (radio: completa, rapida, personalizada)
duracion_total_minutos (auto-calculado)

[Tabla de paradas]
- Parada (select)
- Orden (number)
- Hora estimada (auto)
- Duración (number, min 15, max 120)
- Actividades (multiselect)
- Notas (textarea)
```

**Validaciones frontend recomendadas:**
- Duración total ≤ 240 min
- Cada parada 15-120 min
- Paradas ordenadas y únicas
- Fecha no pasada

---

### `guias/eventos/show.blade.php`
**Propósito:** Ver detalles y gestionar evento

**Secciones:**
1. **Información del evento**
   - Estado con badge
   - Duración total
   - Cupos

2. **Itinerario**
   - Tabla de paradas con: orden, hora, duración, actividades, estado
   - Acciones: cambiar estado de parada

3. **Inscripciones**
   - Lista de turistas inscritos
   - Estado de inscripción
   - Opción eliminar inscripción

4. **Botones de acción**
   - Editar evento
   - Cambiar estado (programado → en_curso → finalizado)
   - Eliminar (solo si estado = 'programado')

---

## 🎨 Estilos Actuales

**Framework CSS:** Ninguno (vanilla CSS)

**Archivos:**
- `resources/css/app.css` (probablemente vacío o con estilos básicos)

**Componentes básicos:**
```css
.btn              /* botón gris */
.btn-sec          /* botón secundario */
.estado           /* badge de estado */
.estado-programado
.estado-en_curso
.estado-finalizado
.estado-cancelado
.rol-badge        /* badge de rol */
```

---

## 🚀 Mejoras Propuestas para Frontend

### Tier 1: Críticas (ASAP)
- [ ] Usar un framework CSS (Bootstrap 5, Tailwind, Pico CSS)
- [ ] Mejorar responsividad (mobile-first)
- [ ] Agregar loading states
- [ ] Validaciones frontend (HTML5 + JS)
- [ ] Better error messages

### Tier 2: Importantes (Pronto)
- [ ] Toast notifications (Toastr, Notyf)
- [ ] Confirm dialogs para acciones destructivas
- [ ] Spinner en botones que hacen submit
- [ ] Breadcrumbs navegables
- [ ] Sidebar colapsable (mobile)

### Tier 3: Nice-to-Have
- [ ] Calendario visual para seleccionar fecha
- [ ] Mapa interactivo de paradas (Google Maps)
- [ ] Drag & drop para reordenar paradas
- [ ] Modo oscuro
- [ ] PWA (Progressive Web App)

---

## 📱 Rutas Web por Rol

```
TURISTA
├── GET /home
├── GET /eventos (listado públicos)
├── GET /eventos/{id} (detalles + inscribirse)
├── POST /eventos/{id}/inscripciones (inscribirse)
└── DELETE /inscripciones/{id} (cancelar)

GUÍA
├── GET /home
├── GET /guias/eventos (mis eventos)
├── GET /guias/eventos/create (crear)
├── POST /guias/eventos (guardar)
├── GET /guias/eventos/{id} (detalles)
├── GET /guias/eventos/{id}/edit (editar)
├── PUT /guias/eventos/{id} (actualizar)
├── PATCH /guias/eventos/{id}/estado (cambiar estado)
├── DELETE /guias/eventos/{id} (eliminar)
└── PATCH /guias/inscripciones/{id} (actualizar estado inscripción)

ADMIN
└── Acceso a todo (igual a guía + más)

GUEST (sin auth)
├── GET /login
├── POST /login
├── GET /register
└── POST /register
```

---

## 💡 Tips para Mejorar la UX

### 1. Tabla de Eventos → Card Layout
**Actual:**
```blade
<table>
    @foreach($eventos as $evento)
        <tr>...</tr>
    @endforeach
</table>
```

**Propuesto:**
```blade
<div class="eventos-grid">
    @foreach($eventos as $evento)
        <div class="evento-card">
            <h3>{{ $evento->tour->nombre }}</h3>
            <p>{{ $evento->fecha->format('d M Y') }}</p>
            <p>{{ $evento->guia->name }}</p>
            <!-- badges de estado, cupos, etc -->
            <a href="{{ route('eventos.show', $evento) }}">Ver detalle</a>
        </div>
    @endforeach
</div>
```

### 2. Formulario de Evento → Step-by-step
**Actual:** Un formulario largo con tabla de paradas inline

**Propuesto:**
```
Step 1: Datos básicos (tour, fecha, hora, cupo)
Step 2: Seleccionar tipo de ruta
Step 3: Confirmar paradas + actividades
Step 4: Resumen y crear
```

### 3. Estados Visuales
```blade
@php
$colores = [
    'programado' => 'bg-blue-100 text-blue-800',
    'en_curso' => 'bg-yellow-100 text-yellow-800',
    'finalizado' => 'bg-green-100 text-green-800',
    'cancelado' => 'bg-red-100 text-red-800',
];
@endphp

<span class="badge {{ $colores[$evento->estado] }}">
    {{ $evento->estado }}
</span>
```

### 4. Countdown para eventos próximos
```blade
@php
$diferencia = $evento->fecha->diffInDays(now());
@endphp

@if($diferencia > 0)
    <p>Faltan {{ $diferencia }} días</p>
@elseif($evento->estado === 'en_curso')
    <p class="alert-warning">¡EN VIVO AHORA!</p>
@endif
```

### 5. Confirmación antes de eliminar
```blade
<button 
    class="btn-danger"
    onclick="return confirm('¿Eliminar este evento?');"
    formmethod="POST"
    formaction="{{ route('guias.eventos.destroy', $evento) }}"
>
    Eliminar
</button>
```

---

## 📊 Formularios Complejos - Recomendaciones

### Formulario de Evento (Paradas dinámicas)

**Problema actual:** Paradas hardcodeadas en HTML

**Solución propuesta:** JavaScript para agregar/quitar paradas dinámicamente

```html
<div id="paradas-container">
    <div class="parada-row" data-index="0">
        <select name="paradas[0][parada_id]">...</select>
        <input type="number" name="paradas[0][orden]">
        <input type="time" name="paradas[0][hora_estimada]">
        <input type="number" name="paradas[0][duracion_minutos]">
        <button type="button" onclick="removerParada(0)">Quitar</button>
    </div>
</div>

<button type="button" onclick="agregarParada()">+ Agregar parada</button>

<script>
let paradaIndex = 1;

function agregarParada() {
    const html = `
        <div class="parada-row" data-index="${paradaIndex}">
            <select name="paradas[${paradaIndex}][parada_id]">...</select>
            <!-- otros campos -->
            <button type="button" onclick="removerParada(${paradaIndex})">Quitar</button>
        </div>
    `;
    document.getElementById('paradas-container').insertAdjacentHTML('beforeend', html);
    paradaIndex++;
}

function removerParada(index) {
    document.querySelector(`[data-index="${index}"]`).remove();
}
</script>
```

---

## 🔒 Seguridad en Vistas

### CSRF Protection
```blade
<!-- Todas las formas deben incluir token CSRF -->
<form method="POST" action="/...">
    @csrf
    <!-- campos -->
</form>
```

### Autorización en Vistas
```blade
<!-- Solo mostrar "Editar" si es owner o admin -->
@can('update', $evento)
    <a href="{{ route('guias.eventos.edit', $evento) }}">Editar</a>
@endcan

<!-- O con Role -->
@if(auth()->user()->hasRole(['guia', 'admin']))
    <a href="{{ route('guias.eventos.create') }}">Crear evento</a>
@endif
```

### Escapar datos
```blade
<!-- ✅ BIEN: Blade escapa automáticamente -->
{{ $evento->tour->nombre }}

<!-- ❌ MAL: HTML sin escapar -->
{!! $evento->descripcion_sin_confiar !!}

<!-- ✅ BIEN: HTML de confianza -->
{!! $eventoDescripcionDelBackend !!}
```

---

## 📱 Responsive Design

### Breakpoints recomendados (Tailwind)
```css
sm: 640px    /* tablets pequeños */
md: 768px    /* tablets */
lg: 1024px   /* laptops */
xl: 1280px   /* desktops */
```

### Grillas
```blade
<!-- Desktop: 3 columnas, Tablet: 2, Mobile: 1 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($eventos as $evento)
        <div class="evento-card">...</div>
    @endforeach
</div>
```

### Navegación mobile
```blade
<nav class="navbar">
    <button class="hamburger" onclick="toggleMenu()">☰</button>
    <ul id="menu" class="menu-mobile hidden md:flex">
        <li><a href="/home">Inicio</a></li>
        <li><a href="/eventos">Eventos</a></li>
        @if(auth()->user()->hasRole('guia'))
            <li><a href="/guias/eventos">Mis eventos</a></li>
        @endif
    </ul>
</nav>
```

---

## 🎯 Next Steps para Mejorar

1. **Elegir un framework CSS**
   - Bootstrap 5 (familiar, muchos componentes)
   - Tailwind (moderno, flexible)
   - Pico CSS (minimalista, simple)

2. **Crear componentes reutilizables**
   ```blade
   <!-- resources/views/components/evento-card.blade.php -->
   <div class="evento-card">
       <h3>{{ $evento->tour->nombre }}</h3>
       @include('components.estado-badge', ['estado' => $evento->estado])
       <p>{{ $evento->fecha->format('d M') }} • {{ $evento->horaInicio() }}</p>
   </div>
   ```

3. **Agregar validaciones frontend**
   - HTML5 validation attributes
   - JavaScript para validaciones complejas

4. **Mejorar feedback de usuario**
   - Loading states
   - Toast notifications
   - Form error highlighting

---

**Última actualización:** 27 de Septiembre, 2026
