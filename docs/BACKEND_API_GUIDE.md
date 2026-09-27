# Backend API Guide - La Ruta del Chocolate

## 📋 Tabla de Contenidos

1. [Autenticación](#autenticación)
2. [Estructura de Datos](#estructura-de-datos)
3. [Roles y Permisos](#roles-y-permisos)
4. [Endpoints Disponibles](#endpoints-disponibles)
5. [Flujos de Uso Común](#flujos-de-uso-común)
6. [Modelos y Relaciones](#modelos-y-relaciones)
7. [Estados y Transiciones](#estados-y-transiciones)

---

## Autenticación

### Login
**POST** `/login`

```json
{
  "email": "usuario@example.com",
  "password": "password"
}
```

**Respuesta:**
- Crea una sesión
- Redirige a `/home`

### Registro (Solo Turistas)
**POST** `/register`

```json
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

**Nota:** El rol por defecto es `turista`

### Logout
**POST** `/logout`

Destruye la sesión y redirige a login.

---

## Estructura de Datos

### Modelos Principales

#### User
```php
id              // int
name            // string
email           // string (unique)
password        // string (hashed)
roles           // relación many-to-many (turista, guia, admin)
created_at      // timestamp
updated_at      // timestamp
```

#### Tour
```php
id              // int
nombre          // string: "La Ruta del Chocolate"
descripcion     // text
activo          // boolean (default: true)
paradas         // relación hasMany -> Parada
ruta_tipos      // relación hasMany -> RutaTipo
eventos         // relación hasMany -> Evento
```

#### Parada
```php
id              // int
nombre          // string: "Para Ti", "Taboada", etc.
descripcion     // text
ubicacion       // string
orden           // int (posición base en la ruta)
activa          // boolean
actividades     // relación belongsToMany -> Actividad
tours           // relación belongsToMany -> Tour
```

#### Actividad
```php
id              // int
parada_id       // int (FK)
nombre          // string: "Trivia", "Quiz Foto", etc.
descripcion     // text
tipo            // string: trivia, quiz_foto, minijuego, etc.
puntaje_maximo  // int
parada           // relación belongsTo -> Parada
participaciones // relación hasMany -> Participacion
```

#### Evento (Instancia de un Tour)
```php
id                      // int
tour_id                 // int (FK)
guia_id                 // int (FK -> User)
fecha                   // date
hora_inicio             // time
cupo_maximo             // int
estado                  // enum: programado, en_curso, finalizado, cancelado
tipo_ruta               // enum: completa, rapida, personalizada
duracion_total_minutos  // int (max 240)
created_at              // timestamp
updated_at              // timestamp

// Relaciones
tour                    // belongsTo -> Tour
guia                    // belongsTo -> User
paradas                 // belongsToMany -> Parada (via EventoParada)
eventoParadas           // hasMany -> EventoParada
inscripciones           // hasMany -> Inscripcion
participaciones         // hasMany -> Participacion
fotos                   // hasMany -> Foto
```

#### EventoParada (Pivot con datos)
```php
id                      // int
evento_id               // int (FK)
parada_id               // int (FK)
orden                   // int (posición en esta ruta específica)
hora_estimada           // time
duracion_minutos        // int (15-120)
actividades_ids         // json array [1, 3, 5]
notas_guia              // text
estado                  // enum: pendiente, en_curso, completada, saltada

// Relaciones
evento                  // belongsTo -> Evento
parada                  // belongsTo -> Parada
```

#### Inscripcion (Turista en Evento)
```php
id                      // int
evento_id               // int (FK)
user_id                 // int (FK -> User)
estado                  // enum: confirmada, cancelada, pendiente
fecha_inscripcion       // timestamp
observaciones           // text

// Relaciones
evento                  // belongsTo -> Evento
turista                 // belongsTo -> User
```

#### Participacion (Actividad completada)
```php
id                      // int
evento_id               // int (FK)
actividad_id            // int (FK)
user_id                 // int (FK -> User)
puntaje_obtenido        // int
fecha_participacion     // timestamp
datos_adicionales       // json

// Relaciones
evento                  // belongsTo -> Evento
actividad               // belongsTo -> Actividad
usuario                 // belongsTo -> User
```

#### Foto
```php
id                      // int
evento_id               // int (FK)
user_id                 // int (FK -> User)
actividad_id            // int FK (optional)
parada_id               // int FK (optional)
url                     // string (path en storage/fotos/)
fecha_captura           // timestamp

// Relaciones
evento                  // belongsTo -> Evento
usuario                 // belongsTo -> User
```

#### RutaTipo (Template de ruta)
```php
id                      // int
tour_id                 // int (FK)
nombre                  // string: "Completa", "Rápida", "Centro"
descripcion             // text
duracion_minutos        // int
activa                  // boolean
paradas_ruta            // relación hasMany -> RutaTipoParada
```

#### RutaTipoParada (Parada en un template)
```php
id                      // int
ruta_tipo_id            // int (FK)
parada_id               // int (FK)
orden                   // int
duracion_minutos        // int (recomendado)
actividades_ids         // json array (recomendadas)
```

---

## Roles y Permisos

### Roles Disponibles
1. **turista**: Usuario común
   - Ver eventos públicos
   - Inscribirse a eventos
   - Participar en actividades
   - Subir fotos
   - Ver su perfil

2. **guia**: Guía turístico
   - Todas las permisos de turista
   - Crear/editar/eliminar eventos propios
   - Ver dashboard de eventos
   - Cambiar estado de evento
   - Marcar actividades como completadas
   - Ver ranking en tiempo real
   - Finalizar evento (genera recuerdos)

3. **admin**: Administrador
   - Acceso total a todo
   - Gestionar tours, paradas, actividades
   - Ver eventos de cualquier guía
   - Gestionar usuarios

### Middleware de Protección

```php
// Solo usuarios autenticados
Route::middleware('auth')->group(...)

// Solo guías y admins
Route::middleware('role:guia|admin')->group(...)

// Solo usuarios sin sesión
Route::middleware('guest')->group(...)
```

---

## Endpoints Disponibles

### Autenticación

| Método | Endpoint | Rol | Descripción |
|--------|----------|-----|-------------|
| GET | `/login` | guest | Mostrar formulario de login |
| POST | `/login` | guest | Procesar login |
| GET | `/register` | guest | Mostrar formulario de registro |
| POST | `/register` | guest | Procesar registro |
| POST | `/logout` | auth | Cerrar sesión |

### Home / Dashboard

| Método | Endpoint | Rol | Descripción |
|--------|----------|-----|-------------|
| GET | `/home` | auth | Dashboard principal (rol-específica) |

### Eventos Públicos (Turista)

| Método | Endpoint | Rol | Descripción |
|--------|----------|-----|-------------|
| GET | `/eventos` | auth | Listar eventos disponibles |
| GET | `/eventos/{id}` | auth | Ver detalles de un evento |

### Inscripciones (Turista)

| Método | Endpoint | Rol | Descripción |
|--------|----------|-----|-------------|
| POST | `/eventos/{evento}/inscripciones` | auth | Inscribirse a un evento |
| DELETE | `/inscripciones/{id}` | auth | Cancelar inscripción |
| PATCH | `/guias/inscripciones/{id}` | guia\|admin | Cambiar estado inscripción |

### Eventos (Guía)

| Método | Endpoint | Rol | Descripción |
|--------|----------|-----|-------------|
| GET | `/guias/eventos` | guia\|admin | Listar eventos del guía |
| GET | `/guias/eventos/create` | guia\|admin | Formulario crear evento |
| POST | `/guias/eventos` | guia\|admin | Crear evento |
| GET | `/guias/eventos/{id}` | guia\|admin | Ver detalles evento |
| GET | `/guias/eventos/{id}/edit` | guia\|admin | Formulario editar evento |
| PUT | `/guias/eventos/{id}` | guia\|admin | Actualizar evento |
| PATCH | `/guias/eventos/{id}/estado` | guia\|admin | Cambiar estado |
| DELETE | `/guias/eventos/{id}` | guia\|admin | Eliminar evento |

---

## Flujos de Uso Común

### Flujo 1: Turista se Inscribe a un Evento

```
1. GET /login
   ├─ Muestra form login
   
2. POST /login
   ├─ Autentica usuario
   └─ Redirige a /home
   
3. GET /home
   ├─ Muestra dashboard turista
   └─ Lista próximos eventos
   
4. GET /eventos
   ├─ Turista ve todos los eventos disponibles
   └─ Puede filtrar por estado/tour
   
5. GET /eventos/{id}
   ├─ Ve detalles: fecha, hora, guía, paradas, actividades
   ├─ Verifica cupos disponibles
   └─ Botón "Inscribirse"
   
6. POST /eventos/{id}/inscripciones
   ├─ Crea inscripción
   ├─ Valida: evento abierto, cupos disponibles
   ├─ Estado por defecto: 'confirmada'
   └─ Redirige a evento con confirmación
```

**Request:**
```json
POST /eventos/1/inscripciones
Content-Type: application/x-www-form-urlencoded

// No requiere datos adicionales (user viene de Auth)
```

**Respuesta:**
```
redirect to /eventos/1 with message "Inscripción confirmada"
```

---

### Flujo 2: Guía Crea un Evento (Ruta Predefinida)

```
1. GET /guias/eventos
   ├─ Muestra todos sus eventos
   └─ Botón "Nuevo evento"
   
2. GET /guias/eventos/create
   ├─ Carga tours + paradas
   ├─ Carga rutas predefinidas
   └─ Muestra formulario interactivo
   
3. POST /guias/eventos
   ├─ Datos: tour, fecha, hora, cupo, tipo_ruta
   ├─ Ruta: lista de paradas con orden, hora, duración, actividades
   ├─ Valida: duración ≤ 240 min, paradas ordenadas, etc.
   └─ Crea evento + EventoParadas
   
4. GET /guias/eventos/{id}
   └─ Muestra evento creado
```

**Request:**
```json
POST /guias/eventos
Content-Type: application/x-www-form-urlencoded

tour_id=1
fecha=2026-10-15
hora_inicio=09:00
cupo_maximo=25
tipo_ruta=completa
duracion_total_minutos=240

// Paradas (array)
paradas[0][parada_id]=1
paradas[0][orden]=1
paradas[0][hora_estimada]=09:00
paradas[0][duracion_minutos]=50
paradas[0][actividades_ids]=1,3
paradas[0][notas_guia]=Museo

paradas[1][parada_id]=2
paradas[1][orden]=2
paradas[1][hora_estimada]=10:00
paradas[1][duracion_minutos]=60
paradas[1][actividades_ids]=5,6,7
paradas[1][notas_guia]=Fábrica
```

---

### Flujo 3: Evento en Progreso

```
1. Guía cambia evento a "en_curso"
   PATCH /guias/eventos/{id}/estado
   └─ Primera parada pasa a estado 'en_curso'
   
2. Durante el evento, turista participa en actividades
   └─ App móvil registra participaciones y fotos
   
3. Al finalizar, guía cambia a "finalizado"
   PATCH /guias/eventos/{id}/estado
   ├─ Todas las paradas pasan a 'completada'
   └─ Genera "recuerdos" (PENDIENTE IMPLEMENTAR)
```

---

## Modelos y Relaciones

### Diagrama Relacional

```
User (1)
├─ roles (M:M via model_has_roles)
├─ eventos (guias) (1:N -> Evento)
├─ inscripciones (1:N -> Inscripcion)
└─ participaciones (1:N -> Participacion)

Tour (1)
├─ paradas (1:N -> Parada, via RutaTour)
├─ eventos (1:N -> Evento)
└─ ruta_tipos (1:N -> RutaTipo)

Parada (1)
├─ actividades (M:M -> Actividad)
├─ evento_paradas (1:N -> EventoParada)
└─ tours (M:M -> Tour)

Actividad (1)
├─ parada (N:1 -> Parada)
└─ participaciones (1:N -> Participacion)

Evento (1)
├─ tour (N:1 -> Tour)
├─ guia (N:1 -> User)
├─ paradas (M:M via EventoParada)
├─ evento_paradas (1:N -> EventoParada)
├─ inscripciones (1:N -> Inscripcion)
├─ participaciones (1:N -> Participacion)
└─ fotos (1:N -> Foto)

EventoParada (pivot con datos)
├─ evento (N:1 -> Evento)
└─ parada (N:1 -> Parada)

Inscripcion (1)
├─ evento (N:1 -> Evento)
└─ turista (N:1 -> User)

Participacion (1)
├─ evento (N:1 -> Evento)
├─ actividad (N:1 -> Actividad)
└─ usuario (N:1 -> User)

Foto (1)
├─ evento (N:1 -> Evento)
├─ usuario (N:1 -> User)
├─ actividad (N:1 -> Actividad, nullable)
└─ parada (N:1 -> Parada, nullable)

RutaTipo (1)
├─ tour (N:1 -> Tour)
└─ paradas_ruta (1:N -> RutaTipoParada)

RutaTipoParada (pivot con datos)
├─ ruta_tipo (N:1 -> RutaTipo)
└─ parada (N:1 -> Parada)
```

---

## Estados y Transiciones

### Estado de Evento

```
Estados: programado → en_curso → finalizado
                   ↘    ↙    ↘    ↙
                   cancelado
```

| Estado | Descripción | Acepta inscripciones | Cambios permitidos |
|--------|-------------|----------------------|-------------------|
| **programado** | Creado, no ha comenzado | ✅ Sí | → en_curso, → cancelado |
| **en_curso** | Tour en progreso | ✅ Sí | → finalizado, → cancelado |
| **finalizado** | Tour completado | ❌ No | (ninguno) |
| **cancelado** | Tour cancelado | ❌ No | → programado |

### Estado de EventoParada

| Estado | Descripción |
|--------|-------------|
| **pendiente** | No ha comenzado |
| **en_curso** | El grupo está actualmente en esta parada |
| **completada** | La parada fue completada |
| **saltada** | Se omitió por algún motivo |

### Estado de Inscripcion

| Estado | Descripción |
|--------|-------------|
| **confirmada** | Turista inscrito, participará |
| **cancelada** | Turista canceló su inscripción |
| **pendiente** | Esperando confirmación (si aplica) |

---

## Validaciones

### Evento (GuardarEventoRequest)

```php
// tour_id: debe existir en tabla tours
tour_id → required|exists:tours,id

// fecha: no puede ser pasada
fecha → required|date|after_or_equal:today

// hora_inicio: formato válido
hora_inicio → required|date_format:H:i

// cupo_maximo: entre 1 y 200
cupo_maximo → required|integer|min:1|max:200

// tipo_ruta: valores permitidos
tipo_ruta → required|in:completa,rapida,personalizada

// duracion_total_minutos: ≤ 240
duracion_total_minutos → required|integer|min:60|max:240

// paradas: al menos 1
paradas → required|array|min:1

// Cada parada
paradas.*.parada_id → required|exists:paradas,id
paradas.*.orden → required|integer|min:1
paradas.*.duracion_minutos → required|integer|min:15|max:120
paradas.*.actividades_ids → nullable|array
```

---

## Tips para Frontend

### 1. URLs Amigables
- Usa rutas con nombre: `route('eventos.show', $evento)`
- Mejor que hardcodear URLs

### 2. Relaciones Eager Loading
- El backend ya carga relaciones necesarias
- Varias queries ya vienen optimizadas con `with()` y `withCount()`

### 3. Formatos de Datos
- Fechas: `Y-m-d` (backend cuida casting)
- Horas: `H:i` o `H:i:s`
- Booleanos: `true/false` o `1/0`

### 4. Métodos Útiles en Modelos
```php
$evento->horaInicio()        // "09:00"
$evento->horaFinalizacion()  // "13:00"
$evento->cuposRestantes()    // int: 5
$evento->estaLleno()         // bool
$evento->admiteInscripciones()  // bool
$evento->estaInscrito($user) // bool
$evento->rutaOrdenada()      // Collection de paradas
$evento->puedeTransicionarA($estado) // bool
```

### 5. Estado de Evento
```php
// Constantes disponibles
Evento::ESTADO_PROGRAMADO
Evento::ESTADO_EN_CURSO
Evento::ESTADO_FINALIZADO
Evento::ESTADO_CANCELADO

// Para mostrar en UI
@if($evento->estado === Evento::ESTADO_PROGRAMADO)
    Puedes inscribirse
@endif
```

---

## Ejemplos de Consultas Útiles

### Listar eventos próximos (public)
```php
Evento::abiertosALasInscripciones()
    ->proximos()
    ->with('tour', 'guia')
    ->get();
```

### Mis eventos (guía)
```
Evento::deGuia(auth()->user())
    ->recientes()
    ->with('tour')
    ->withCount(['inscripciones as inscritos'])
    ->get();
```

### Evento con todas las relaciones
```php
Evento::with([
    'tour',
    'guia',
    'paradas' => fn($q) => $q->orderBy('evento_parada.orden'),
    'inscripciones' => fn($q) => $q->where('estado', 'confirmada'),
    'participaciones',
    'fotos'
])->find($id);
```

---

## Próximos Pasos (Pendiente Implementar)

- [ ] Sistema de fotos (upload a storage)
- [ ] Generación de recuerdos (PDF/imagen)
- [ ] API REST para mobile (actualmente solo web)
- [ ] Sistema de actividades interactivas (registro en tiempo real)
- [ ] Ranking en vivo durante evento
- [ ] Notificaciones (email/SMS)
- [ ] Exportación de reportes

---

**Última actualización:** 27 de Septiembre, 2026
