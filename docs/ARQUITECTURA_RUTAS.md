# Arquitectura de Rutas Variables - La Ruta del Chocolate

## Visión General

El sistema implementa un **enfoque híbrido** para rutas variables que permite:
- **Rutas Predefinidas:** Templates reutilizables para operaciones estándar
- **Rutas Personalizadas:** Flexibilidad total para adaptar cada evento a condiciones específicas
- **Duración Limitada:** Máximo 4 horas (240 minutos) por evento

---

## Estructura de Datos

### Modelos Principales

#### 1. **Evento** (Instancia de un Tour)
```php
- tour_id          // El tour base
- guia_id          // Quién guía
- fecha            // Cuándo
- hora_inicio      // A qué hora
- cupo_maximo      // Cuántos turistas máximo
- tipo_ruta        // 'completa', 'rapida', 'personalizada'
- duracion_total_minutos  // Estimación total
- estado           // programado, en_curso, finalizado, cancelado
```

#### 2. **EventoParada** (Pivot - Detalles de cada parada en el evento)
```php
- evento_id                // Referencia al evento
- parada_id               // Referencia a la parada
- orden                   // Posición en la ruta
- hora_estimada           // A qué hora llega
- duracion_minutos        // Tiempo dedicado (15-120 min)
- actividades_ids         // JSON: IDs de actividades seleccionadas
- notas_guia              // Observaciones específicas del guía
- estado                  // pendiente, en_curso, completada, saltada
```

#### 3. **RutaTipo** (Template de Ruta)
```php
- tour_id                 // Tour asociado
- nombre                  // 'Ruta Completa', 'Ruta Rápida', etc.
- descripcion            // Qué incluye
- duracion_minutos       // Duración estimada
- activo                 // Está disponible o no
```

#### 4. **RutaTipoParada** (Paradas en una RutaTipo)
```php
- ruta_tipo_id           // RutaTipo asociada
- parada_id              // Parada en esta ruta
- orden                  // Posición
- duracion_minutos       // Tiempo dedicado
- actividades_ids        // JSON: actividades recomendadas
```

---

## Estrategias de Creación de Eventos

### 1️⃣ Ruta Predefinida (Recomendada para Empezar)

El guía selecciona un tipo de ruta existente:

```
Tour Base: "La Ruta del Chocolate"
  ├─ Ruta Completa (240 min)
  │   ├─ Para Ti (Museo) - 50 min
  │   ├─ Taboada (Fábrica) - 50 min
  │   ├─ Mibombón (Sucursal) - 40 min
  │   └─ Sucre (Sucursal) - 50 min
  │
  ├─ Ruta Rápida (150 min)
  │   ├─ Para Ti (Museo) - 70 min
  │   └─ Taboada (Fábrica) - 70 min
  │
  └─ Ruta Centro (120 min)
      ├─ Mibombón (Sucursal) - 50 min
      └─ Sucre (Sucursal) - 50 min
```

**Ventajas:**
- Predecible para turistas
- Fácil de comunicar
- Rápido de crear

**Cuándo usar:**
- Operaciones normales
- Rutas bien establecidas
- Tours concurrentes similares

---

### 2️⃣ Ruta Personalizada (Máxima Flexibilidad)

El guía selecciona paradas individuales el día del evento:

```php
$rutaService->crearEventoPersonalizado([
    'tour_id' => 1,
    'guia_id' => auth()->id(),
    'fecha' => '2026-10-15',
    'hora_inicio' => '09:00',
    'cupo_maximo' => 20,
    'duracion_total_minutos' => 180,
], [
    // Parada 1: Para Ti
    [
        'parada_id' => 1,
        'duracion_minutos' => 45,
        'actividades_ids' => [1, 3],  // Trivia y Quiz Foto
        'notas_guia' => 'Enfatizar el museo hoy'
    ],
    // Parada 2: Taboada
    [
        'parada_id' => 2,
        'duracion_minutos' => 60,
        'actividades_ids' => [5, 6, 7],  // Todas las actividades
        'notas_guia' => 'Grupo interesado en proceso de producción'
    ],
]);
```

**Ventajas:**
- Máxima flexibilidad
- Adapta a condiciones del día
- Personalizado por grupo

**Cuándo usar:**
- Clima impredecible
- Grupo con intereses especiales
- Cierre de una parada
- Tours a medida

---

## Flujo de Uso

### Crear un Evento (POST /guias/eventos)

```javascript
{
  "tour_id": 1,
  "fecha": "2026-10-15",
  "hora_inicio": "09:00",
  "cupo_maximo": 25,
  "tipo_ruta": "completa",  // O 'rapida', 'personalizada'
  "duracion_total_minutos": 240,
  "paradas": [
    {
      "id": 1,  // Para Ti
      "orden": 1,
      "hora_estimada": "09:00",
      "duracion_minutos": 50,
      "actividades_ids": [1, 3],
      "notas_guia": "Museo - detenerse en sección de historia"
    },
    {
      "id": 2,  // Taboada
      "orden": 2,
      "hora_estimada": "10:00",
      "duracion_minutos": 60,
      "actividades_ids": [5, 6],
      "notas_guia": "Fábrica - enfatizar proceso artesanal"
    }
  ]
}
```

### Actualizar Ruta (PUT /guias/eventos/{id})

Mismo formato. El sistema recalcula horas y duraciones automáticamente.

---

## Servicios Disponibles

### RutaService

```php
use App\Services\RutaService;

$rutaService = app(RutaService::class);

// Crear desde ruta predefinida
$evento = $rutaService->crearEventoDesdeTipo(
    ['tour_id' => 1, 'guia_id' => 1, 'fecha' => '2026-10-15', ...],
    'completa'  // tipo_ruta
);

// Crear personalizado
$evento = $rutaService->crearEventoPersonalizado(
    $datos,
    $paradasIds
);

// Actualizar ruta de evento existente
$rutaService->actualizarRuta($evento, $nuevasParadas);

// Obtener rutas disponibles
$rutas = $rutaService->obtenerRutasDisponibles($tourId);

// Validar duración
$valido = $rutaService->validarDuracionTotal(240);
```

---

## Cálculos Automáticos

### Hora Estimada de Llegada
Basado en:
1. Hora de inicio del evento
2. Tiempo de tránsito entre paradas (15 min por defecto)
3. Duración de cada parada

**Ejemplo:**
```
Inicio: 09:00
P1: 09:00 + 0 tránsito + 50 min = 09:50
P2: 09:50 + 15 tránsito + 60 min = 11:05
P3: 11:05 + 15 tránsito + 40 min = 12:00
```

### Duración Total
Se calcula como:
```
duracion_total_minutos = SUM(duracion_minutos) + (num_paradas - 1) * 15
```

---

## Seeder - Rutas Predefinidas

Ejecutar:
```bash
php artisan db:seed --class=RutaTipoSeeder
```

Crea 3 rutas:
1. **Ruta Completa** (240 min)
   - Todas las paradas
   - Todas las actividades

2. **Ruta Rápida** (150 min)
   - Solo destinos turísticos (Para Ti, Taboada)
   - 2-3 actividades máximo

3. **Ruta Centro** (120 min)
   - Solo sucursales cercanas
   - 1 actividad máximo

---

## Estados de Parada en Evento

| Estado | Significado |
|--------|-------------|
| `pendiente` | No ha comenzado |
| `en_curso` | El grupo está ahí |
| `completada` | Terminó la parada |
| `saltada` | Se omitió por algún motivo |

---

## Validaciones

### En Formulario (GuardarEventoRequest)
- ✅ Fecha no puede ser en el pasado
- ✅ Duración máxima 4 horas (240 min)
- ✅ Cupo mínimo 1, máximo 200
- ✅ Paradas deben ser únicas y ordenadas
- ✅ Actividades deben existir en la BD

### En Lógica de Negocio
- ✅ No superar 240 minutos totales
- ✅ Mínimo 60 minutos (1 hora)
- ✅ Cada parada 15-120 minutos

---

## Ejemplo Real: Diferentes Rutas en Diferentes Días

### Lunes (Llovía)
```
Tipo: Rápida
09:00 - Para Ti (45 min, 2 actividades)
10:00 - Taboada (60 min, 3 actividades)
11:00 - Fin
Duración: 120 min
```

### Miércoles (Normal)
```
Tipo: Completa
09:00 - Para Ti (50 min, 3 actividades)
10:00 - Taboada (50 min, 3 actividades)
11:00 - Mibombón (40 min, 2 actividades)
11:40 - Sucre (50 min, 2 actividades)
12:30 - Fin
Duración: 240 min
```

### Viernes (Grupo Especializado)
```
Tipo: Personalizada
09:00 - Para Ti (60 min, taller exclusivo)
10:15 - Taboada (70 min, degustación especial)
11:30 - Tienda Artesanal (40 min, notas: "Primera vez ofreciendo")
12:10 - Fin
Duración: 130 min
Notas: Grupo de chefs, interesados en técnicas
```

---

## Próximos Pasos

1. ✅ Modelos y migraciones
2. ✅ Servicios de lógica
3. ⏳ Vistas para crear/editar eventos
4. ⏳ Mostrar rutas a turistas
5. ⏳ Marcar progreso en tiempo real
6. ⏳ Generar recuerdos al finalizar

---

## Contacto & Notas

- **Máxima duración:** 4 horas (realista para experiencias locales)
- **Paradas base:** 4 chocolaterías (pero escalable)
- **Actividades por parada:** 2-3 (configurable)
- **Grupos típicos:** 20-30 turistas por evento
