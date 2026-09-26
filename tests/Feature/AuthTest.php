<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'guia', 'turista'] as $rol) {
            Role::findOrCreate($rol);
        }
    }

    public function test_registro_crea_turista_con_rol_y_autentica(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Nueva Turista',
            'email' => 'nueva@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'consentimiento_marketing' => 1,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::where('email', 'nueva@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('turista'));
        $this->assertTrue($user->consentimiento_marketing);
    }

    public function test_registro_no_permite_email_duplicado(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('register'), [
            'name' => 'Otra Persona',
            'email' => $user->email,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_autentica_y_redirige_al_home(): void
    {
        $user = User::factory()->create();
        $user->assignRole('turista');

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fallido_devuelve_error(): void
    {
        $user = User::factory()->create();

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_home_requiere_autenticacion(): void
    {
        $this->get(route('home'))->assertRedirect(route('login'));
    }

    public function test_usuario_autenticado_ve_su_rol_en_home(): void
    {
        $user = User::factory()->create();
        $user->assignRole('guia');

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('guia');
    }

    public function test_logout_cierra_sesion(): void
    {
        $user = User::factory()->create();
        $user->assignRole('turista');

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}