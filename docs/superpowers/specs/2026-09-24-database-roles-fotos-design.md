# Diseño: Base de datos, roles y almacenamiento de fotos

**Fecha:** 2026-09-24
**Proyecto:** La Ruta del Chocolate — sitio promocional (Sucre, Bolivia)
**Alcance:** Creación de la base de datos MySQL a partir de `PREBD/`, roles con Spatie y decisión de almacenamiento de fotos.

---

## 1. Stack

- Laravel 12 (PHP 8.2) sobre XAMPP.
- MySQL / MariaDB (XAMPP) como motor de base de datos; revisión visual con MySQL Workbench.
- `spatie/laravel-permission` para roles.
- Vista previas server-rendered con Blade (la fase de vistas es posterior a este alcance).

## 2. Base de datos

- Nombre de la BD: `ruta_chocolate` (utf8mb4), creada desde migraciones.
- Fuente de verdad: migraciones de `PREBD/` movidas a `database/migrations/` **sin cambios de estructura**.
- `DB_CONNECTION=mysql` en `.env` apuntando a XAMPP (`127.0.0.1`, `root`, sin contraseña).

### Tablas

Las 3 por defecto de Laravel (`users`, `cache`, `jobs`) + 5 de Spatie (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) + 9 de PREBD:

| Tabla | Columnas principales | Notas |
|---|---|---|
| `users` | + telefono, ciudad_origen, consentimiento_marketing, avatar | Campos agregados por migración PREBD `000001` |
| `tours` | nombre, slug (único), descripcion, imagen_portada, activo | softDeletes |
| `paradas` | tour_id, nombre, descripcion, orden, latitud, longitud, imagen_principal | softDeletes; índice (tour_id, orden) |
| `eventos` | tour_id, guia_id→users, fecha, hora_inicio, cupo_maximo, estado | estado: programado/en_curso/finalizado/cancelado |
| `evento_parada` | evento_id, parada_id, hora_estimada, orden, estado | pivot; único (evento_id, parada_id) |
| `inscripciones` | evento_id, user_id, estado, fecha_inscripcion | único (evento_id, user_id) |
| `actividades` | parada_id, nombre, tipo, configuracion (JSON), puntos_max, activo | tipo: trivia/quiz_foto/encuentra_diferencia/otro |
| `participaciones` | evento_id, actividad_id, user_id, puntaje, intentos, completado_en | único (evento_id, actividad_id, user_id); índice ranking (evento_id, puntaje) |
| `fotos` | evento_id, user_id, parada_id (nullable), ruta_archivo, tipo | tipo: actividad/libre |

### Modelos Eloquent

- `User` → trait `HasRoles` de Spatie.
- `Tour` → `hasMany` `Parada`.
- `Parada` → `belongsTo` `Tour`, `hasMany` `Actividad`.
- `Evento` → `belongsTo` `Tour`, `belongsTo` `Guia` (User), `hasMany` `EventoParada`, `Inscripcion`, `Participacion`, `Foto`.
- `EventoParada` → modelo para el pivot con estado propio.
- `Inscripcion` → `belongsTo` `Evento` y `User`.
- `Actividad` → `belongsTo` `Parada`.
- `Participacion` → `belongsTo` `Evento`, `Actividad`, `User`.
- `Foto` → `belongsTo` `Evento`, `User`, `Parada` (nullable).

### Orden de migración

Las migraciones PREBD (timestamps 2024-01-01) corren después de las 3 por defecto (0001-01-01) y antes de las de Spatie. `php artisan migrate:fresh --seed` debe ejecutarse de principio a fin sin errores.

## 3. Roles con Spatie

- **admin** — control total: gestiona guías, eventos, tours y paradas.
- **guia** — crea/edita eventos, inicia el tour, ve ranking en vivo y finaliza el evento.
- **turista** — se registra, ve itinerario, se inscribe, participa en actividades y sube fotos.

Autorización por rol vía `role:...` en rutas (definidas en la fase de vistas). Sin permisos granulares en este alcance.

## 4. Almacenamiento de fotos

- Disco público local: `storage/app/public` + `php artisan storage:link`.
- Subcarpetas por dominio:
  - `avatars/`
  - `tours/`
  - `paradas/`
  - `fotos/eventos/{evento_id}/`
- `ruta_archivo` guarda la ruta relativa al disco.
- Sin cloud (S3). El recuerdo final se genera al vuelo desde `fotos` + `participaciones`; no hay tabla extra, no se guarda nada adicional.

## 5. Seeders

`DatabaseSeeder`:
1. Roles Spatie: admin, guia, turista.
2. Usuario admin inicial (`admin@rutachocolate.test` / `password`).
3. Tour **"La Ruta del Chocolate"** con slug, descripción e imagen.
4. Paradas representativas de Sucre (con orden, coordenadas, descripciones).
5. Actividades de ejemplo (2–3 por parada) con `configuracion` JSON.
6. Eventos de prueba: uno programado, uno en curso, uno finalizado, cada uno con guía, paradas del tour, inscripciones, participaciones y fotos de ejemplo.

## 6. Operación y flujos

- Turista se registra → se inscribe a un evento (único por evento+user) → participa en actividades (puntajes) y sube fotos → cuando el guía marca el evento como finalizado, cada turista ve su recuerdo (collage + logros) calculado al momento.
- Ranking en vivo = suma de `participaciones.puntaje` por evento y usuario.

## 7. Verificación y control de errores

- Criterio de éxito: `php artisan migrate:fresh --seed` corre limpio.
- Credenciales MySQL correctas en `.env`.
- Ejecutar `storage:link` para acceso público a fotos.
- Validación de unicidad en `inscripciones` y `participaciones` desde el modelo (reglas/controladores se definen en la fase de vistas).
- Tests de rutas (PHPUnit) quedan para la fase de vistas; este alcance valida con migrar + sembrar.

## 8. Fuera de alcance (fase siguiente)

- Vistas Blade, controladores, rutas y autenticación con roles.
- Generación visual del recuerdo.
- Actividades interactivas (juegos).
- Permisos granulares si las vistas lo requieren.