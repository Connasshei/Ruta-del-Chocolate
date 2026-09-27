# Implementation Guide - Sistema Completo de Fotos, Actividades y Recuerdos

## 🚀 Quick Start (30 minutos)

### 1. Ejecutar Migraciones
```bash
cd C:\xampp\htdocs\RutaDelChocolate
php artisan migrate
```

**Crea:**
- Tabla `recuerdos` (para guardar PDFs/HTML generados)

### 2. Crear Directorio de Almacenamiento
```bash
# Ya existen, pero asegurar permisos
# Windows: No hay problema de permisos
```

### 3. Crear Enlace Simbólico (si no existe)
```bash
php artisan storage:link
# Crea: public/storage → storage/app/public
```

---

## 📋 Nuevas Funcionalidades Implementadas

### 1. Sistema de Fotos ✅

**Modelos:**
- `Foto` (ya existía)

**Services:**
- `FotoService` (upload, validación, almacenamiento)

**Controllers:**
- `FotoController` (galería, subir, eliminar, descargar)

**Rutas:**
```php
GET    /eventos/{evento}/fotos                     // Galería
POST   /eventos/{evento}/fotos                     // Subir
DELETE /eventos/{evento}/fotos/{foto}              // Eliminar
GET    /eventos/{evento}/fotos/{foto}/descargar   // Descargar
```

**Vistas:**
- `fotos/galeria.blade.php` (galería con tabs)

**Almacenamiento:**
- Disco: `fotos` (storage/app/fotos)
- Ruta: `evento_{id}/{user_id}/{timestamp}.jpg`
- Máximo: 5MB por imagen
- Formatos: JPG, PNG, WebP

---

### 2. Actividades Interactivas ✅

**Modelos:**
- `Actividad` (ya existía)
- `Participacion` (ya existía)

**Services:**
- `ActividadService` (registrar participación, ranking, estadísticas)

**Controllers:**
- `ActividadController` (mostrar, participar, ranking, resumen)

**Rutas:**
```php
GET    /eventos/{evento}/actividades/{actividad}              // Mostrar actividad
POST   /eventos/{evento}/actividades/{actividad}/participar   // Registrar participación
GET    /eventos/{evento}/actividades/resumen                  // Resumen del turista
GET    /guias/eventos/{evento}/ranking                        // Ranking en vivo (guía)
```

**Vistas:**
- `actividades/mostrar.blade.php` (muestra actividad + tipo)
- `actividades/tipos/trivia.blade.php` (preguntas múltiple)
- `actividades/tipos/quiz-foto.blade.php` (elegir foto correcta)
- `actividades/ranking.blade.php` (ranking en vivo)
- `actividades/resumen-turista.blade.php` (mi resumen)

**Tipos de Actividades Soportados:**
1. **Trivia** (preguntas con opciones múltiples)
2. **Quiz Foto** (elegir imagen correcta)

**Configuración de Actividad (JSON):**
```json
{
  "descripcion": "Responde las siguientes preguntas...",
  "preguntas": [
    {
      "texto": "¿Cuál es la temperatura ideal del cacao?",
      "respuesta_correcta": 1,
      "opciones": [
        {"texto": "20°C"},
        {"texto": "35°C"},
        {"texto": "50°C"}
      ]
    }
  ]
}
```

---

### 3. Generación de Recuerdos ✅

**Modelos:**
- `Recuerdo` (NUEVO)

**Services:**
- `RecuerdoService` (generar PDF/HTML, almacenar, descargar)

**Controllers:**
- `RecuerdoController` (mi recuerdo, ver, descargar, galería)

**Rutas:**
```php
GET    /eventos/{evento}/recuerdos/mi-recuerdo     // Mi recuerdo
GET    /eventos/{evento}/recuerdos/{recuerdo}      // Ver recuerdo
GET    /eventos/{evento}/recuerdos/{recuerdo}/descargar  // Descargar
GET    /guias/eventos/{evento}/recuerdos           // Galería (guía)
```

**Vistas:**
- `recuerdos/mi-recuerdo.blade.php` (mi recuerdo con preview)
- `recuerdos/galeria.blade.php` (galería para guía)

**Generación Automática:**
- Se dispara cuando `evento->estado` cambia a `finalizado`
- Crea un recuerdo HTML por cada turista inscrito
- Incluye: fotos, actividades, puntuaciones
- Almacenado en: `storage/app/public/recuerdos/evento_{id}/{filename}.html`

---

## 🔧 Configuración

### Filesystems (filesystems.php)
```php
'fotos' => [
    'driver' => 'local',
    'root' => storage_path('app/fotos'),
    'url' => '/storage/fotos',
    'visibility' => 'public',
],
```

### Permisos
- Carpeta `storage/app/fotos` debe existir (automático)
- Carpeta `storage/app/public` debe existir (automático)
- Enlace simbólico: `public/storage → storage/app/public`

---

## 📝 Requests Nuevos

### SubirFotoRequest
```php
// Validaciones
'imagen' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
'parada_id' => ['nullable', 'integer', 'exists:paradas,id'],
'tipo' => ['required', 'in:actividad,libre'],
```

### RegistrarParticipacionRequest
```php
// Validaciones
'puntaje' => ['required', 'integer', 'min:0'],
'intentos' => ['nullable', 'integer', 'min:1', 'max:10'],
```

---

## 🧪 Prueba Todo Funcione

### 1. Crear un Evento de Prueba
```
Login como guia@rutachocolate.test
/guias/eventos/create
Crear evento para hoy + 3 días
```

### 2. Subir Foto (como turista)
```
Login como turista@rutachocolate.test
/eventos (ver evento)
/eventos/{id}/fotos
Subir una imagen
```

### 3. Completar Actividad
**Primero, crear una actividad:**
```bash
php artisan tinker
$actividad = App\Models\Actividad::create([
    'parada_id' => 1,
    'nombre' => 'Trivia del Cacao',
    'tipo' => 'trivia',
    'puntos_max' => 100,
    'configuracion' => [
        'descripcion' => 'Responde correctamente las preguntas',
        'preguntas' => [
            [
                'texto' => '¿Cuál es el origen del cacao?',
                'respuesta_correcta' => 1,
                'opciones' => [
                    ['texto' => 'Asia'],
                    ['texto' => 'América'],
                    ['texto' => 'Europa']
                ]
            ]
        ]
    ]
]);
```

**Luego:**
```
Como turista: /eventos/{id}/actividades/{id}
Responder trivia
POST registra participación y puntuación
```

### 4. Generar Recuerdo
```
Como guía: /guias/eventos/{id}/estado
Cambiar a "finalizado"
Sistema dispara recuerdo automáticamente
Como turista: /eventos/{id}/recuerdos/mi-recuerdo
Ver recuerdo generado
```

---

## 🐛 Troubleshooting

### "Disco 'fotos' no encontrado"
**Solución:** Asegurar que `config/filesystems.php` tiene la sección `'fotos'`

### "No se puede escribir en storage/fotos"
**Solución Windows:** 
```bash
# Dar permisos a la carpeta (usualmente no es necesario en XAMPP)
icacls "storage/app/fotos" /grant:r "%username%:F"
```

### "Las fotos no se ven"
**Solución:** Ejecutar
```bash
php artisan storage:link
```

### "Recuerdos vacíos/incompletos"
**Verificar:**
1. Turista está inscrito (estado = 'confirmada')
2. Turista completó actividades
3. Evento fue marcado como finalizado

---

## 📚 Próximas Mejoras (Para el Frontend)

1. **Mejoras UI:**
   - Usar Bootstrap o Tailwind
   - Galería de fotos con lightbox
   - Progress bar para trivia
   - Timer para actividades (opcional)

2. **Features Avanzadas:**
   - Upload drag & drop de fotos
   - Contador de puntos en vivo
   - Compartir recuerdo (email/WhatsApp)
   - Certificado personalizado

3. **Seguridad:**
   - Validar que turista está inscrito antes de cada acción
   - CSRF tokens (ya está)
   - Limitar intentos por actividad

---

## 🔌 API Endpoints (para móvil/frontend separado)

Todas las rutas funcionan con formularios POST/PUT/DELETE.
Para API REST JSON, se puede crear controllers adicionales en `api/` routes.

---

## 📊 Base de Datos - Tablas Nuevas

### recuerdos
```sql
CREATE TABLE recuerdos (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    evento_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    ruta_archivo VARCHAR(255),
    datos_generacion JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(evento_id, user_id),
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## ✅ Checklist de Implementación

- [x] Modelos creados
- [x] Migraciones creadas
- [x] Services creados
- [x] Controllers creados
- [x] Requests creados
- [x] Rutas agregadas
- [x] Vistas básicas creadas
- [x] Filesystems configurados
- [ ] Tests unitarios (pendiente)
- [ ] Tests e2e (pendiente)
- [ ] UI mejorada (pendiente para frontend)

---

## 🚀 Próximo Paso

```bash
# 1. Ejecutar migración
php artisan migrate

# 2. Crear symlink
php artisan storage:link

# 3. Probar manualmente (ver arriba)

# 4. Equipo frontend mejora vistas con Bootstrap/Tailwind
```

---

**Última actualización:** 27 de Septiembre, 2026

**Tiempo implementación:** 4-5 horas

**Lineas de código:** ~2000 (backend + vistas básicas)
