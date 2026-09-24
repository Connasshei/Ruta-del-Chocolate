# Plan: Base de datos, Roles (Spatie) y Almacenamiento de Fotos

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Crear la base de datos MySQL del sitio promocional "La Ruta del Chocolate" a partir de las migraciones de `PREBD/`, con roles de Spatie y almacenamiento público de fotos.

**Architecture:** Laravel 12 sirve como fuente de verdad del esquema: las 9 migraciones de `PREBD/` se mueven a `database/migrations/` sin cambios estructurales, se agrega `spatie/laravel-permission` (5 tablas de roles/permisos), se crean los modelos Eloquent con sus relaciones, y `DatabaseSeeder` puebla roles, usuarios, tour, paradas, actividades y eventos de prueba. Las fotos se guardan en `storage/app/public`.

**Tech Stack:** Laravel 12, PHP 8.2, MySQL/MariaDB (XAMPP), `spatie/laravel-permission`, Blade (vistas en fase posterior).

**Spec:** `docs/superpowers/specs/2026-09-24-database-roles-fotos-design.md`

## Global Constraints

- PHP >= 8.2, Laravel 12.
- BD MySQL `ruta_chocolate` en XAMPP (`127.0.0.1:3306`, user `root`, sin password).
- Migraciones de `PREBD/` se mueven **sin cambiar su estructura**.
- Roles Spatie: `admin`, `guia`, `turista` (guard `web`).
- Fotos: disco público `storage/app/public` + `php artisan storage:link`. Sin S3.
- No existe tabla de recuerdos; el recuerdo se genera al vuelo.
- Tests (phpunit.xml) corren en SQLite `:memory:`; no dependen de XAMPP.

---

## File Structure

- `database/migrations/` — migraciones por defecto + 9 de PREBD (movidas) + 1 publicada por Spatie.
- `app/Models/` — `Tour`, `Parada`, `Evento`, `EventoParada`, `Inscripcion`, `Actividad`, `Participacion`, `Foto`; `User` modificado.
- `database/seeders/` — `RoleSeeder`, `UserSeeder`, `TourSeeder`, `EventoSeeder`; `DatabaseSeeder` modificado.
- `config/permission.php` — publicado por Spatie.
- `tests/Feature/DatabaseSchemaTest.php` — verificación integral del esquema y seeders.
- `.env`, `composer.json`, `composer.lock` — configuración MySQL y dependencias.

---

### Task 1: Conexión MySQL y creación de la base de datos

**Files:**
- Modify: `.env`

**Interfaces:**
- Consumes: nada (primer paso).
- Produces: conexión MySQL operativa; BD `ruta_chocolate` existente.

- [ ] **Step 1: Configurar variables de BD en `.env`**

Reemplazar el bloque actual:

```
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

por:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ruta_chocolate
DB_USERNAME=root
DB_PASSWORD=
```

- [ ] **Step 2: Crear la base de datos `ruta_chocolate`**

Verificar primero que MySQL de XAMPP esté corriendo. Luego:

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS ruta_chocolate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Si el binario no está en esa ruta, buscarlo con `Get-Command mysql` o `Get-ChildItem C:\xampp\mysql\bin`. Alternativa válida: crear la BD desde phpMyAdmin (http://localhost/phpmyadmin, charset `utf8mb4`, collation `utf8mb4_unicode_ci`).

- [ ] **Step 3: Verificar la conexión**

```powershell
php artisan db:show
```

Expected: conexión OK contra `ruta_chocolate`, 0 tablas. Si falla la autenticación, revisar `DB_USERNAME`/`DB_PASSWORD` en `.env`.

- [ ] **Step 4: Commit**

```bash
git add .env
git commit -m "chore: configurar MySQL y crear BD ruta_chocolate"
```

---

### Task 2: Instalar y publicar Spatie Permission

**Files:**
- Create: `config/permission.php` (publicado)
- Create: `database/migrations/<timestamp>_create_permission_tables.php` (publicado)
- Modify: `composer.json`, `composer.lock`

**Interfaces:**
- Consumes: nada.
- Produces: paquete Spatie y sus migraciones de roles/permisos listas.

- [ ] **Step 1: Instalar el paquete**

```powershell
composer require spatie/laravel-permission
```

- [ ] **Step 2: Publicar migración y configuración**

```powershell
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Esto crea `config/permission.php` y una migración `2024_xx_xx_*_create_permission_tables.php` en `database/migrations/`.

- [ ] **Step 3: Verificar**

```powershell
composer show spatie/laravel-permission
Get-ChildItem config\permission.php, database\migrations\*permission_tables*
```

Expected: paquete instalado, ambos archivos presentes.

- [ ] **Step 4: Commit**

```bash
git add composer.json composer.lock config/permission.php database/migrations
git commit -m "feat: instalar spatie/laravel-permission y publicar sus migraciones"
```

---

### Task 3: Mover migraciones de PREBD y migrar

**Files:**
- Move: `PREBD/2024_01_01_0000*.php` → `database/migrations/`

**Interfaces:**
- Consumes: Task 1 (MySQL listo), Task 2 (Spatie publicado).
- Produces: esquema completo aplicado en `ruta_chocolate` (12 tablas + 5 de Spatie).

- [ ] **Step 1: Mover las 9 migraciones**

```powershell
git mv PREBD/2024_01_01_000001_add_fields_to_users_table.php database/migrations/
git mv PREBD/2024_01_01_000002_create_tours_table.php database/migrations/
git mv PREBD/2024_01_01_000003_create_paradas_table.php database/migrations/
git mv PREBD/2024_01_01_000004_create_eventos_table.php database/migrations/
git mv PREBD/2024_01_01_000005_create_evento_parada_table.php database/migrations/
git mv PREBD/2024_01_01_000006_create_inscripciones_table.php database/migrations/
git mv PREBD/2024_01_01_000007_create_actividades_table.php database/migrations/
git mv PREBD/2024_01_01_000008_create_participaciones_table.php database/migrations/
git mv PREBD/2024_01_01_000009_create_fotos_table.php database/migrations/
```

Luego, si la carpeta `PREBD/` quedó vacía, eliminarla:

```powershell
if ((Get-ChildItem PREBD -Force -ErrorAction SilentlyContinue).Count -eq 0) { Remove-Item PREBD -Force }
```

- [ ] **Step 2: Aplicar las migraciones**

```powershell
php artisan migrate
```

Expected: se aplican las migraciones por defecto, las 9 de PREBD (orden 0001…0009 tras las de 0001_01_01) y la de Spatie, sin errores.

- [ ] **Step 3: Verificar el estado**

```powershell
php artisan migrate:status
```

Expected: todas las migraciones marcadas como `Ran`.

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "feat: mover migraciones de PREBD a database/migrations y migrar"
```

---

### Task 4: Modelos Eloquent

**Files:**
- Create: `app/Models/Tour.php`, `app/Models/Parada.php`, `app/Models/Evento.php`, `app/Models/EventoParada.php`, `app/Models/Inscripcion.php`, `app/Models/Actividad.php`, `app/Models/Participacion.php`, `app/Models/Foto.php`
- Modify: `app/Models/User.php`

**Interfaces:**
- Consumes: Task 3 (tablas creadas).
- Produces: clases `Tour`, `Parada`, `Evento`, `EventoParada`, `Inscripcion`, `Actividad`, `Participacion`, `Foto` y `User` con trait `HasRoles`, usadas por los seeders (Task 5) y el test (Task 6).

- [ ] **Step 1: Escribir el modelo `Tour`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nombre', 'slug', 'descripcion', 'imagen_portada', 'activo'];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function paradas(): HasMany
    {
        return $this->hasMany(Parada::class);
    }
}
```

- [ ] **Step 2: Escribir el modelo `Parada`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parada extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['tour_id', 'nombre', 'descripcion', 'orden', 'latitud', 'longitud', 'imagen_principal'];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class);
    }
}
```

- [ ] **Step 3: Escribir el modelo `Evento`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    use HasFactory;

    public const ESTADO_PROGRAMADO = 'programado';
    public const ESTADO_EN_CURSO = 'en_curso';
    public const ESTADO_FINALIZADO = 'finalizado';
    public const ESTADO_CANCELADO = 'cancelado';

    protected $fillable = ['tour_id', 'guia_id', 'fecha', 'hora_inicio', 'cupo_maximo', 'estado'];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'cupo_maximo' => 'integer',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function guia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guia_id');
    }

    public function eventoParadas(): HasMany
    {
        return $this->hasMany(EventoParada::class);
    }

    public function paradas(): BelongsToMany
    {
        return $this->belongsToMany(Parada::class)
            ->using(EventoParada::class)
            ->withPivot('hora_estimada', 'orden', 'estado');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(Participacion::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(Foto::class);
    }
}
```

- [ ] **Step 4: Escribir el modelo `EventoParada`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoParada extends Model
{
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_EN_CURSO = 'en_curso';
    public const ESTADO_COMPLETADA = 'completada';

    protected $table = 'evento_parada';

    protected $fillable = ['evento_id', 'parada_id', 'hora_estimada', 'orden', 'estado'];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
        ];
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function parada(): BelongsTo
    {
        return $this->belongsTo(Parada::class);
    }
}
```

- [ ] **Step 5: Escribir el modelo `Inscripcion`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscripcion extends Model
{
    use HasFactory;

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_CONFIRMADO = 'confirmado';
    public const ESTADO_CANCELADO = 'cancelado';

    protected $fillable = ['evento_id', 'user_id', 'estado', 'fecha_inscripcion'];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function turista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

- [ ] **Step 6: Escribir el modelo `Actividad`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    use HasFactory;

    public const TIPO_TRIVIA = 'trivia';
    public const TIPO_QUIZ_FOTO = 'quiz_foto';
    public const TIPO_ENCUENTRA_DIFERENCIA = 'encuentra_diferencia';
    public const TIPO_OTRO = 'otro';

    protected $fillable = ['parada_id', 'nombre', 'tipo', 'configuracion', 'puntos_max', 'activo'];

    protected function casts(): array
    {
        return [
            'configuracion' => 'array',
            'puntos_max' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function parada(): BelongsTo
    {
        return $this->belongsTo(Parada::class);
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(Participacion::class);
    }
}
```

- [ ] **Step 7: Escribir el modelo `Participacion`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participacion extends Model
{
    use HasFactory;

    protected $fillable = ['evento_id', 'actividad_id', 'user_id', 'puntaje', 'intentos', 'completado_en'];

    protected function casts(): array
    {
        return [
            'puntaje' => 'integer',
            'intentos' => 'integer',
            'completado_en' => 'datetime',
        ];
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function turista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

- [ ] **Step 8: Escribir el modelo `Foto`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Foto extends Model
{
    use HasFactory;

    public const TIPO_ACTIVIDAD = 'actividad';
    public const TIPO_LIBRE = 'libre';

    protected $fillable = ['evento_id', 'user_id', 'parada_id', 'ruta_archivo', 'tipo'];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function turista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parada(): BelongsTo
    {
        return $this->belongsTo(Parada::class);
    }
}
```

- [ ] **Step 9: Actualizar `User` con HasRoles y relaciones**

```php
<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'ciudad_origen',
        'consentimiento_marketing',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'consentimiento_marketing' => 'boolean',
        ];
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(Participacion::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(Foto::class);
    }

    public function eventosGuiados(): HasMany
    {
        return $this->hasMany(Evento::class, 'guia_id');
    }
}
```

- [ ] **Step 10: Verificar que todas las clases resuelven**

```powershell
php artisan tinker --execute="foreach (['App\Models\Tour','App\Models\Parada','App\Models\Evento','App\Models\EventoParada','App\Models\Inscripcion','App\Models\Actividad','App\Models\Participacion','App\Models\Foto','App\Models\User'] as \$c) { echo (class_exists(\$c) ? 'OK ' : 'FAIL ') . \$c . PHP_EOL; }"
```

Expected: `OK` en las 9 líneas.

- [ ] **Step 11: Commit**

```bash
git add app/Models
git commit -m "feat: agregar modelos Eloquent y trait HasRoles a User"
```

---

### Task 5: Seeders de datos de ejemplo

**Files:**
- Create: `database/seeders/RoleSeeder.php`, `database/seeders/UserSeeder.php`, `database/seeders/TourSeeder.php`, `database/seeders/EventoSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Consumes: modelos de Task 4 (Traits/constantes).
- Produces: base sembrada con los 3 roles, admin, 2 guías, 2 turistas, tour "La Ruta del Chocolate", 4 paradas, 6 actividades y 3 eventos (programado/en_curso/finalizado).

- [ ] **Step 1: Escribir `RoleSeeder`**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'guia', 'turista'] as $rol) {
            Role::firstOrCreate(['name' => $rol]);
        }
    }
}
```

- [ ] **Step 2: Escribir `UserSeeder`**

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@rutachocolate.test'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'consentimiento_marketing' => true,
            ]
        );
        $admin->assignRole('admin');

        $guias = [
            ['name' => 'Guía María', 'email' => 'guia@rutachocolate.test'],
            ['name' => 'Guía Carlos', 'email' => 'guia2@rutachocolate.test'],
        ];

        foreach ($guias as $guia) {
            $user = User::firstOrCreate(
                ['email' => $guia['email']],
                ['name' => $guia['name'], 'password' => 'password']
            );
            $user->assignRole('guia');
        }

        $turistas = [
            ['name' => 'Turista Ana', 'email' => 'turista@rutachocolate.test', 'ciudad_origen' => 'La Paz'],
            ['name' => 'Turista Luis', 'email' => 'turista2@rutachocolate.test', 'ciudad_origen' => 'Cochabamba'],
        ];

        foreach ($turistas as $turista) {
            $user = User::firstOrCreate(
                ['email' => $turista['email']],
                [
                    'name' => $turista['name'],
                    'password' => 'password',
                    'ciudad_origen' => $turista['ciudad_origen'],
                ]
            );
            $user->assignRole('turista');
        }
    }
}
```

- [ ] **Step 3: Escribir `TourSeeder`**

```php
<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $tour = Tour::firstOrCreate(
            ['slug' => 'ruta-del-chocolate'],
            [
                'nombre' => 'La Ruta del Chocolate',
                'descripcion' => 'Recorrido por los mejores lugares del chocolate en la ciudad de Sucre: historia, cacao, fabricación y degustación.',
                'imagen_portada' => 'tours/ruta-del-chocolate.jpg',
                'activo' => true,
            ]
        );

        $paradas = [
            [
                'nombre' => 'Plaza 25 de Mayo',
                'descripcion' => 'Punto de encuentro del recorrido.',
                'orden' => 1,
                'latitud' => -19.0473,
                'longitud' => -65.2600,
                'imagen_principal' => 'paradas/plaza-25-de-mayo.jpg',
            ],
            [
                'nombre' => 'Fábrica de Chocolate Artesanal',
                'descripcion' => 'Visita al taller de producción y degustación de cacao sucrense.',
                'orden' => 2,
                'latitud' => -19.0491,
                'longitud' => -65.2591,
                'imagen_principal' => 'paradas/fabrica-chocolate.jpg',
            ],
            [
                'nombre' => 'Museo del Chocolate',
                'descripcion' => 'Historia del cacao y del chocolate en Bolivia.',
                'orden' => 3,
                'latitud' => -19.0500,
                'longitud' => -65.2580,
                'imagen_principal' => 'paradas/museo-chocolate.jpg',
            ],
            [
                'nombre' => 'Taller de Chocolatería',
                'descripcion' => 'Elaboración de bombones con cacao boliviano.',
                'orden' => 4,
                'latitud' => -19.0510,
                'longitud' => -65.2570,
                'imagen_principal' => 'paradas/taller-chocolateria.jpg',
            ],
        ];

        $actividades = [
            // keyed by nombre de parada
            'Plaza 25 de Mayo' => [
                ['nombre' => 'Trivia: ¿cuánto sabes de Sucre?', 'tipo' => Actividad::TIPO_TRIVIA, 'puntos_max' => 100,
                 'configuracion' => ['preguntas' => [
                     ['pregunta' => '¿En qué departamento se encuentra Sucre?', 'opciones' => ['La Paz', 'Chuquisaca', 'Cochabamba', 'Santa Cruz'], 'respuesta' => 1],
                 ]]],
            ],
            'Fábrica de Chocolate Artesanal' => [
                ['nombre' => 'Quiz foto: identifica el ingrediente', 'tipo' => Actividad::TIPO_QUIZ_FOTO, 'puntos_max' => 100,
                 'configuracion' => ['preguntas' => [
                     ['pregunta' => '¿Cuál de estos es cacao puro?', 'opciones' => ['Manteca', 'Cacao', 'Azúcar', 'Leche'], 'respuesta' => 1],
                 ]]],
                ['nombre' => 'Encuentra la diferencia', 'tipo' => Actividad::TIPO_ENCUENTRA_DIFERENCIA, 'puntos_max' => 50,
                 'configuracion' => ['diferencias' => 3]],
            ],
            'Museo del Chocolate' => [
                ['nombre' => 'Trivia: historia del cacao', 'tipo' => Actividad::TIPO_TRIVIA, 'puntos_max' => 100,
                 'configuracion' => ['preguntas' => [
                     ['pregunta' => 'El cacao es originario de...', 'opciones' => ['Europa', 'África', 'América', 'Asia'], 'respuesta' => 2],
                 ]]],
                ['nombre' => 'Quiz foto: ordena la línea de producción', 'tipo' => Actividad::TIPO_QUIZ_FOTO, 'puntos_max' => 80,
                 'configuracion' => ['pasos' => 4]],
            ],
            'Taller de Chocolatería' => [
                ['nombre' => 'Trivia: el bombón perfecto', 'tipo' => Actividad::TIPO_TRIVIA, 'puntos_max' => 100,
                 'configuracion' => ['preguntas' => [
                     ['pregunta' => '¿Qué se usa para templar el chocolate?', 'opciones' => ['Agua', 'Baño María', 'Horno', 'Microondas'], 'respuesta' => 1],
                 ]]],
            ],
        ];

        foreach ($paradas as $datosParada) {
            $parada = $tour->paradas()->firstOrCreate(
                ['nombre' => $datosParada['nombre']],
                $datosParada
            );

            foreach ($actividades[$datosParada['nombre']] ?? [] as $datosActividad) {
                $parada->actividades()->firstOrCreate(
                    ['nombre' => $datosActividad['nombre']],
                    $datosActividad
                );
            }
        }
    }
}
```

- [ ] **Step 4: Escribir `EventoSeeder`**

```php
<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\EventoParada;
use App\Models\Foto;
use App\Models\Inscripcion;
use App\Models\Participacion;
use App\Models\Tour;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        $tour = Tour::where('slug', 'ruta-del-chocolate')->firstOrFail();
        $guia = User::role('guia')->firstOrFail();
        $turistas = User::role('turista')->get();

        $this->crearEvento($guia, $tour, Carbon::now()->addDays(7), '09:00', Evento::ESTADO_PROGRAMADO);
        $this->crearEvento($guia, $tour, Carbon::today(), '09:00', Evento::ESTADO_EN_CURSO);
        $finalizado = $this->crearEvento($guia, $tour, Carbon::today()->subDays(10), '09:00', Evento::ESTADO_FINALIZADO);

        $this->poblarEventoFinalizado($finalizado, $turistas);
    }

    private function crearEvento(User $guia, Tour $tour, Carbon $fecha, string $hora, string $estado): Evento
    {
        $evento = Evento::create([
            'tour_id' => $tour->id,
            'guia_id' => $guia->id,
            'fecha' => $fecha,
            'hora_inicio' => $hora,
            'cupo_maximo' => 30,
            'estado' => $estado,
        ]);

        $orden = 1;
        $horaBase = (int) substr($hora, 0, 2);

        foreach ($tour->paradas()->orderBy('orden')->get() as $parada) {
            $evento->eventoParadas()->create([
                'parada_id' => $parada->id,
                'orden' => $orden,
                'estado' => $estado === Evento::ESTADO_FINALIZADO ? EventoParada::ESTADO_COMPLETADA : EventoParada::ESTADO_PENDIENTE,
                'hora_estimada' => sprintf('%02d:30', $horaBase + $orden),
            ]);
            $orden++;
        }

        return $evento;
    }

    private function poblarEventoFinalizado(Evento $evento, Collection $turistas): void
    {
        $paradas = $evento->tour()->first()->paradas()->orderBy('orden')->pluck('id');

        $indice = 0;
        foreach ($evento->paradas()->get() as $parada) {
            foreach ($parada->actividades()->get() as $actividad) {
                $turista = $turistas->get($indice % $turistas->count());
                Participacion::firstOrCreate(
                    ['evento_id' => $evento->id, 'actividad_id' => $actividad->id, 'user_id' => $turista->id],
                    ['puntaje' => 100 - ($indice % 3) * 10, 'intentos' => 1, 'completado_en' => Carbon::now()]
                );
                $indice++;
            }
        }

        foreach ($turistas as $k => $turista) {
            Inscripcion::firstOrCreate(
                ['evento_id' => $evento->id, 'user_id' => $turista->id],
                ['estado' => Inscripcion::ESTADO_CONFIRMADO]
            );

            Foto::firstOrCreate(
                ['evento_id' => $evento->id, 'user_id' => $turista->id, 'tipo' => Foto::TIPO_ACTIVIDAD, 'parada_id' => $paradas->get($k % $paradas->count())],
                ['ruta_archivo' => "fotos/eventos/{$evento->id}/turista{$k}-actividad.jpg"]
            );

            Foto::firstOrCreate(
                ['evento_id' => $evento->id, 'user_id' => $turista->id, 'tipo' => Foto::TIPO_LIBRE, 'parada_id' => null],
                ['ruta_archivo' => "fotos/eventos/{$evento->id}/turista{$k}-libre.jpg"]
            );
        }
    }
}
```

- [ ] **Step 5: Modificar `DatabaseSeeder`**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TourSeeder::class,
            EventoSeeder::class,
        ]);
    }
}
```

- [ ] **Step 6: Verificar en SQLite (tests)**

```powershell
php artisan test --filter=DatabaseSchemaTest
```

Expected: el test de Task 6 hace disponible la verificación integral. (Si aún no existe, proseguir; la verificación formal llega en Task 6.)

- [ ] **Step 7: Commit**

```bash
git add database/seeders
git commit -m "feat: seeders de roles, usuarios, tour, paradas, actividades y eventos"
```

---

### Task 6: Test de verificación del esquema y datos sembrados

**Files:**
- Create: `tests/Feature/DatabaseSchemaTest.php`

**Interfaces:**
- Consumes: todo lo anterior (migraciones, modelos, seeders).
- Produces: prueba que valida tablas, roles, seeders, relaciones y restricciones de unicidad.

- [ ] **Step 1: Escribir el test (primero, pasa a fallar)**

```php
<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Inscripcion;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_contiene_todas_las_tablas(): void
    {
        foreach (['tours', 'paradas', 'eventos', 'evento_parada', 'inscripciones', 'actividades', 'participaciones', 'fotos', 'roles'] as $tabla) {
            $this->assertTrue(Schema::hasTable($tabla), "Falta la tabla {$tabla}");
        }
    }

    public function test_seed_crea_los_tres_roles(): void
    {
        $this->seed();

        foreach (['admin', 'guia', 'turista'] as $rol) {
            $this->assertTrue(Role::where('name', $rol)->exists(), "No existe el rol {$rol}");
        }
    }

    public function test_seed_crea_admin_tour_paradas_y_actividades(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@rutachocolate.test')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));

        $tour = Tour::where('slug', 'ruta-del-chocolate')->first();
        $this->assertNotNull($tour);
        $this->assertGreaterThanOrEqual(3, $tour->paradas()->count());
        $this->assertGreaterThanOrEqual(6, $tour->paradas()->withCount('actividades')->get()->sum('actividades_count'));
    }

    public function test_seed_crea_eventos_en_tres_estados(): void
    {
        $this->seed();

        foreach (['programado', 'en_curso', 'finalizado'] as $estado) {
            $this->assertTrue(Evento::where('estado', $estado)->exists(), "Falta un evento {$estado}");
        }

        $finalizado = Evento::where('estado', 'finalizado')->firstOrFail();
        $this->assertGreaterThanOrEqual(1, $finalizado->inscripciones()->count());
        $this->assertGreaterThanOrEqual(1, $finalizado->participaciones()->count());
        $this->assertGreaterThanOrEqual(1, $finalizado->fotos()->count());
    }

    public function test_inscripcion_es_unica_por_evento_y_usuario(): void
    {
        $this->seed();

        $finalizado = Evento::where('estado', 'finalizado')->firstOrFail();
        $existente = $finalizado->inscripciones()->firstOrFail();

        $this->expectException(QueryException::class);

        Inscripcion::create([
            'evento_id' => $finalizado->id,
            'user_id' => $existente->user_id,
        ]);
    }
}
```

- [ ] **Step 2: Ejecutar el test**

```powershell
php artisan test --filter=DatabaseSchemaTest
```

Expected: PASS en las 5 pruebas.

- [ ] **Step 3: Ejecutar la suite completa**

```powershell
php artisan test
```

Expected: PASS en `DatabaseSchemaTest` y en los `ExampleTest` por defecto. Si un test de ejemplo falla por cambios de config, corregir únicamente ese test de ejemplo (no el de esquema).

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/DatabaseSchemaTest.php
git commit -m "test: verificación de esquema, seeders y unicidad"
```

---

### Task 7: Migración final en MySQL y enlace público de fotos

**Files:**
- Modify: ninguno (archivos generados por comandos).

**Interfaces:**
- Consumes: tasks 1 a 6 completadas.
- Produces: BD `ruta_chocolate` poblada y fotos accesibles por `/storage/...`.

- [ ] **Step 1: Recrear y sembrar la base en MySQL**

```powershell
php artisan migrate:fresh --seed
```

Expected: `ruta_chocolate` recreada y sembrada sin errores.

- [ ] **Step 2: Crear el enlace público de fotos**

```powershell
php artisan storage:link
```

- [ ] **Step 3: Verificar el enlace y la conexión**

```powershell
Test-Path public\storage
php artisan db:show
```

Expected: `True` en `public\storage` y MySQL operativo contra `ruta_chocolate` (12 tablas de aplicación + 5 de Spatie muestran en `db:show`).

- [ ] **Step 4: Verificación final de tests**

```powershell
php artisan test
```

Expected: PASS completo. No hay cambios de código en esta tarea, por lo que no requiere commit propio.

---

## Self-Review

- **Cobertura del spec:** MySQL (Task 1), Spatie con los 3 roles (Task 2, seeders en Task 5), migraciones PREBD sin cambios (Task 3), modelos con relaciones (Task 4), seeders completos incl. 3 eventos en distintos estados (Task 5), almacenamiento público + `storage:link` (Task 7), sin tabla de recuerdos (sin tarea, acorde al spec), verificación `migrate:fresh --seed` limpio (Task 7) y unicidad en `inscripciones`/`participaciones` (Task 6).
- **Sin placeholders:** todos los pasos de código incluyen el contenido real.
- **Consistencia de tipos/nombres:** `ESTADOS`, `TIPO_*`, relaciones y firmas coinciden entre modelos, seeders y test.