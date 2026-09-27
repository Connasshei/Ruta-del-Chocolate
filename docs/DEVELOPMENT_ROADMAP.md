# Development Roadmap - La Ruta del Chocolate

## 📊 Estado General del Proyecto

**Stack:** Laravel 11 + MySQL + Blade Templates

**Avance estimado:** 40% (Backend bien estructurado, Frontend básico)

---

## ✅ Implementado (HECHO)

### Backend - Core
- [x] Autenticación (login, registro, logout)
- [x] Sistema de roles (turista, guía, admin)
- [x] Modelos y migraciones completos
- [x] Arquitectura de rutas variables (híbrida)
- [x] Servicios de lógica (EventoService, RutaService, InscripcionService)
- [x] Validaciones en forms (GuardarEventoRequest)
- [x] Scopes útiles en modelos

### Backend - Controladores
- [x] Auth: LoginController, RegisterController, LogoutController
- [x] Eventos: EventoController (CRUD + cambiar estado)
- [x] Inscripciones: InscripcionController
- [x] Eventos públicos: EventoPublicoController

### Vistas Básicas (Blade)
- [x] Layouts (app, guest)
- [x] Auth (login, register)
- [x] Home dashboard (role-specific)
- [x] Eventos públicos (listado y detalles)
- [x] Eventos guía (listado, crear, editar, mostrar)

### Configuración
- [x] Permisos y roles (Spatie)
- [x] Testing setup con PHPUnit
- [x] Database seeding

---

## ❌ Pendiente (TODO)

### Tier 1: Crítico (MVP)

#### 1. Sistema de Fotos
```php
// Modelo: Foto (ya existe)
// Falta: 
// - Upload endpoint
// - Almacenamiento en storage/fotos
// - Mostrar en vistas
// - Limpiar fotos al eliminar evento

// Estimado: 4-6 horas
```

**Tareas:**
- [ ] Crear middleware/validación de imagen
- [ ] Implementar upload en controller (FormRequest)
- [ ] Guardar en storage/fotos/{evento_id}
- [ ] Mostrar galería en evento
- [ ] Tests de upload

---

#### 2. Actividades Interactivas
```php
// Modelos: Actividad, Participacion (ya existen)
// Falta:
// - Vistas para participar en actividades
// - Registro de puntuación
// - Validación de participación

// Estimado: 6-8 horas
```

**Tareas:**
- [ ] Crear vista "Actividad en Progreso"
- [ ] Formulario de respuesta (varía por tipo: trivia, quiz foto)
- [ ] Controller: registrar participación
- [ ] Validar que turista está inscrito
- [ ] Mostrar resultado/feedback
- [ ] Tests de participación

---

#### 3. Cambio de Estado de Evento (Timeline)
```php
// Lógica existe, falta UI
// Transiciones: programado → en_curso → finalizado

// Estimado: 3-4 horas
```

**Tareas:**
- [ ] Botón "Iniciar tour" (programado → en_curso)
- [ ] Confirmación con modal
- [ ] Actualizar estado EventoParada (primera → en_curso)
- [ ] Botón "Finalizar tour" (en_curso → finalizado)
- [ ] Modal de confirmación
- [ ] Trigger generación de recuerdos (ver abajo)

---

#### 4. Generación de Recuerdos
```php
// Lógica: Cuando evento finaliza
// - Recopilar fotos del turista
// - Contar logros (actividades completadas)
// - Generar PDF/imagen con datos
// - Guardar o enviar por email

// Estimado: 8-12 horas
```

**Tareas:**
- [ ] Crear modelo Recuerdo
- [ ] Servicio para generar PDF (Laravel DomPDF)
- [ ] Template PDF con:
  - Fotos del evento
  - Puntuaciones obtenidas
  - Actividades completadas
  - QR con link para descargar
- [ ] Enviar por email al turista
- [ ] Tests

---

### Tier 2: Importante (Semana 2)

#### 5. Dashboard Mejorado (Guía)
```php
// Actual: Solo listado simple de eventos
// Propuesto:
// - Estadísticas (total turistas, eventos por mes)
// - Gráfico de ocupación
// - Próximos eventos en cards
// - Ranking en vivo durante evento

// Estimado: 6-8 horas
```

**Tareas:**
- [ ] Agregar Chart.js para gráficos
- [ ] Query para estadísticas
- [ ] Refactorizar HomeController
- [ ] Vista dashboard guía mejorada
- [ ] Tests

---

#### 6. API REST (para mobile/frontend separado)
```php
// Endpoints JSON (no blade)
// Útil si futuros equipos quieren hacer app mobile

// Estimado: 10-12 horas
```

**Tareas:**
- [ ] Rutas API (api/v1/)
- [ ] Controllers JSON (EventoController, InscripcionController)
- [ ] Resources (transformar modelos a JSON)
- [ ] Autenticación (Sanctum)
- [ ] Tests API
- [ ] Documentación (OpenAPI/Swagger)

---

#### 7. Mejorar UI/UX Frontend
```php
// Actual: Vanilla CSS + tablas HTML
// Propuesto: Framework CSS moderno

// Estimado: 12-16 horas
```

**Tareas:**
- [ ] Elegir framework (Bootstrap 5 / Tailwind)
- [ ] Actualizar layout base
- [ ] Convertir tablas a cards
- [ ] Agregar colores, espaciado, tipografía
- [ ] Responsive design
- [ ] Toast notifications (Toastr)
- [ ] Form validations

---

#### 8. Email Notifications
```php
// Cuando: 
// - Turista se inscribe
// - Guía crea evento
// - Evento finaliza (con recuerdo)
// - Cancelación

// Estimado: 4-5 horas
```

**Tareas:**
- [ ] Crear Mailable classes
- [ ] Templates email (.blade.php)
- [ ] Queue setup (opcional)
- [ ] Tests

---

### Tier 3: Nice-to-Have (Luego)

#### 9. Búsqueda Avanzada
- [ ] Filtro por ubicación (ciudad/parada)
- [ ] Filtro por rango de fechas
- [ ] Búsqueda por nombre de tour
- [ ] Valoración de eventos
- [ ] Mostrar eventos cercanos (mapa)

#### 10. Landing Page Pública
- [ ] Página inicio sin auth
- [ ] Información sobre "La Ruta del Chocolate"
- [ ] Galería de tours anteriores
- [ ] Testimonios
- [ ] Call-to-action para registrarse

#### 11. Panel Admin
- [ ] Gestionar tours
- [ ] Gestionar paradas
- [ ] Gestionar guías (crear/editar/eliminar)
- [ ] Ver todas las participaciones
- [ ] Reportes

#### 12. PWA & Offline Support
- [ ] Manifest.json
- [ ] Service worker
- [ ] Funcionamiento offline básico
- [ ] Install prompt

#### 13. Notificaciones en Tiempo Real
- [ ] WebSockets (Broadcasting)
- [ ] Notificación cuando evento comienza
- [ ] Chat en vivo durante evento
- [ ] Actualización de ranking en vivo

---

## 🎯 Recomendación de Orden de Implementación

### Semana 1-2 (MVP Core)
**Enfoque:** Completar funcionalidad base

1. **Sistema de Fotos** (Tier 1, 4-6h)
   - Upload simple
   - Galería en evento
   - Borrado automático

2. **Actividades Interactivas** (Tier 1, 6-8h)
   - Vistas para participar
   - Registro de puntuaciones
   - Tipos: trivia simple, quiz foto

3. **Cambio de Estado UI** (Tier 1, 3-4h)
   - Botones para cambiar estado
   - Modal de confirmación

4. **Generación de Recuerdos** (Tier 1, 8-12h)
   - PDF con fotos y puntuaciones
   - Email automático

**Total Semana 1-2:** ~21-30 horas
**Prioridad:** CRÍTICA - Completa el MVP

---

### Semana 3 (Polish)

1. **Mejorar Frontend UI** (Tier 2, 12-16h)
   - Framework CSS
   - Responsive
   - Mejor UX

2. **Email Notifications** (Tier 2, 4-5h)
   - Confirmaciones
   - Notificaciones evento

**Total Semana 3:** ~16-21 horas
**Prioridad:** ALTA - Para que sea usable

---

### Después (Backend preparado para frontend)

1. **API REST** (Tier 2, 10-12h)
   - Para equipo mobile/frontend separado

2. **Dashboard Mejorado** (Tier 2, 6-8h)
   - Estadísticas, gráficos

3. **Tier 3** según necesidad

---

## 📋 Checklist por Funcionalidad

### Fotos del Evento
- [ ] Validación de tipo/tamaño de imagen
- [ ] Upload a storage/fotos/{evento_id}
- [ ] Asociar a turista/parada/actividad
- [ ] Galería en evento (grid de fotos)
- [ ] Lightbox para expandir
- [ ] Borrar foto
- [ ] Limpiar storage al eliminar evento

### Actividades en Evento
- [ ] Pantalla "Actividades en esta parada"
- [ ] Tipos soportados:
  - [ ] Trivia (preguntas con opciones)
  - [ ] Quiz Foto (elegir foto correcta)
  - [ ] Minijuego (simple, score)
- [ ] Formulario de respuesta
- [ ] Validación (turista inscrito)
- [ ] Guardar en Participacion table
- [ ] Mostrar puntuación obtenida
- [ ] Mostrar ranking en vivo (guía)

### Recuerdos
- [ ] Generar automático al finalizar evento
- [ ] Datos incluidos:
  - [ ] Fotos personales del turista
  - [ ] Puntuaciones por actividad
  - [ ] Total de puntos
  - [ ] Actividades completadas
  - [ ] Fecha y tour
  - [ ] QR para descargar
- [ ] Formato: PDF o imagen
- [ ] Enviar por email
- [ ] Permitir descargar desde evento

### Dashboard Guía
- [ ] Tarjetas de eventos próximos
- [ ] Gráfico: ocupación por evento
- [ ] Gráfico: eventos por mes
- [ ] Total de turistas en próximos 30 días
- [ ] Eventos activos hoy (destacado)

### API REST
- [ ] GET /api/v1/eventos (públicos)
- [ ] GET /api/v1/eventos/{id}
- [ ] POST /api/v1/eventos/{id}/inscripciones
- [ ] DELETE /api/v1/inscripciones/{id}
- [ ] POST /api/v1/participaciones (guía, turista)
- [ ] GET /api/v1/eventos/{id}/ranking (guía)
- [ ] Autenticación con tokens (Sanctum)

---

## 🔄 Flujos Principales (Versión Completa)

### Flujo Turista (MVP)
```
1. Registrarse
2. Loguear
3. Ver eventos disponibles
4. Inscribirse a evento
5. Esperar fecha del evento
6. Durante evento:
   - Ver paradas/actividades
   - Participar en actividades
   - Sacar/subir fotos
7. Recibir recuerdo por email
8. Descargar recuerdo
```

### Flujo Guía (MVP)
```
1. Loguear
2. Crear evento (seleccionar paradas, actividades)
3. Esperar inscripciones
4. Iniciar evento (cambiar a en_curso)
5. Durante evento:
   - Ver participaciones en tiempo real
   - Ver ranking
   - Marcar paradas completadas
6. Finalizar evento
7. Sistema genera recuerdos para turistas
8. Ver reporte de evento
```

---

## 🧪 Testing

### Actual
- [ ] Tests básicos de autenticación
- [ ] Tests de modelos (relaciones)

### Pendiente
- [ ] Tests de EventoService (crear, actualizar, cambiar estado)
- [ ] Tests de upload de fotos
- [ ] Tests de participaciones
- [ ] Tests de APIs REST
- [ ] Tests de generación de PDF

---

## 📚 Documentación

### Completada
- [x] ARQUITECTURA_RUTAS.md (rutas variables)
- [x] BACKEND_API_GUIDE.md (endpoints, modelos, flujos)
- [x] FRONTEND_STRUCTURE.md (vistas, mejoras)
- [x] DEVELOPMENT_ROADMAP.md (este documento)

### Falta
- [ ] README.md mejorado (setup local, deploy)
- [ ] Guía de deployment (producción)
- [ ] Troubleshooting (problemas comunes)
- [ ] Swagger/OpenAPI (cuando exista API REST)

---

## 🚀 Quick Start para Desarrollo

### Setup Local
```bash
# Clonar repo
git clone <repo>
cd RutaDelChocolate

# Instalar dependencias
composer install
npm install

# Configuración
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate
php artisan db:seed

# Servir
php artisan serve
# Acceder a http://localhost:8000
```

### Roles de Prueba (del Seeder)
```
admin@rutachocolate.test / password123
guia@rutachocolate.test / password123
turista@rutachocolate.test / password123
```

### Crear Evento de Prueba
```
1. Login como guia@rutachocolate.test
2. Ir a /guias/eventos/create
3. Seleccionar "La Ruta del Chocolate"
4. Fecha futura (ej: hoy + 3 días)
5. Hora: 09:00
6. Cupo: 20
7. Tipo ruta: "completa"
8. Guardar

Ver evento en /guias/eventos/{id}
```

### Inscribir Turista
```
1. Logout
2. Login como turista@rutachocolate.test
3. Ir a /eventos
4. Buscar evento creado
5. Click "Ver detalle"
6. Click "Inscribirse"
```

---

## 💡 Decisiones Importantes

### Fotos
- **Almacenamiento:** storage/fotos/{evento_id}/{user_id}_{timestamp}.jpg
- **Validación:** JPG, PNG, max 5MB
- **Borrado:** Automático al eliminar evento

### Recuerdos
- **Formato:** PDF (con DomPDF)
- **Distribucion:** Email + link de descarga en evento
- **Retención:** Ilimitada (guardar en storage)

### Actividades
- **Tipos soportados inicialmente:** trivia, quiz_foto
- **Escalabilidad:** JSON en field `tipo` para agregar más

### API REST
- **Versión:** v1
- **Autenticación:** Sanctum (tokens)
- **Rate limiting:** Por usuario/IP
- **Responses:** JSON con status codes HTTP estándar

---

## 📞 Contacto / Preguntas

**Sobre arquitectura:** Ver ARQUITECTURA_RUTAS.md
**Sobre endpoints:** Ver BACKEND_API_GUIDE.md
**Sobre vistas:** Ver FRONTEND_STRUCTURE.md
**Sobre estado:** Ver este documento (DEVELOPMENT_ROADMAP.md)

---

**Última actualización:** 27 de Septiembre, 2026

---

## Resumen Rápido

| Aspecto | Estado | Prioridad |
|--------|--------|-----------|
| Core backend | ✅ 90% | - |
| Autenticación | ✅ 100% | - |
| Rutas variables | ✅ 100% | - |
| Fotos | ❌ 0% | 🔴 Alta |
| Actividades | ❌ 20% | 🔴 Alta |
| Recuerdos | ❌ 0% | 🔴 Alta |
| Frontend UI | ⚠️ 30% | 🟠 Media |
| API REST | ❌ 0% | 🟡 Baja |
| Landing page | ❌ 0% | 🟡 Baja |
| Admin panel | ❌ 0% | 🟡 Baja |

**Estimación total MVP:** 30-40 horas de desarrollo
