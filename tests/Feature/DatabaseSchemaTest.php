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