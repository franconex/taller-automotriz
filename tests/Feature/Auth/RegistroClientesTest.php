<?php

namespace Tests\Feature\Auth;

use App\Models\Cliente;
use App\Models\Rol;
use App\Models\User;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\RolPermisoSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class RegistroClientesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolSeeder::class, PermisoSeeder::class, RolPermisoSeeder::class]);
    }

    /* =============================================================
       REGISTRO MANUAL
       ============================================================= */

    public function test_registro_manual_correcto(): void
    {
        $response = $this->post(route('register'), [
            'nombre_completo' => 'Cliente Nuevo',
            'email' => 'nuevo@test.com',
            'telefono' => '70000001',
            'ci' => '1234567',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'terminos' => '1',
        ]);

        $response->assertRedirect(route('cliente.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@test.com',
            'origen_registro' => 'manual',
        ]);

        $this->assertDatabaseHas('clientes', [
            'email' => 'nuevo@test.com',
            'nombre_completo' => 'Cliente Nuevo',
        ]);
    }

    public function test_registro_asigna_rol_cliente(): void
    {
        $response = $this->post(route('register'), [
            'nombre_completo' => 'Test', 'email' => 'test@test.com',
            'password' => 'Password1', 'password_confirmation' => 'Password1',
            'terminos' => '1',
        ]);

        $response->assertRedirect(route('cliente.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'test@test.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Cliente', $user->rol->nombre);
    }

    public function test_registro_no_permite_elegir_rol(): void
    {
        $response = $this->post(route('register'), [
            'nombre_completo' => 'Test', 'email' => 'test2@test.com',
            'password' => 'Password1', 'password_confirmation' => 'Password1',
            'terminos' => '1',
            'rol_id' => Rol::where('nombre', 'Administrador')->first()->id,
        ]);

        $response->assertRedirect(route('cliente.dashboard'));

        $user = User::where('email', 'test2@test.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Cliente', $user->rol->nombre);
    }

    public function test_registro_password_cifrada(): void
    {
        $this->post(route('register'), [
            'nombre_completo' => 'Test', 'email' => 'pwdtest@test.com',
            'password' => 'Password1', 'password_confirmation' => 'Password1',
            'terminos' => '1',
        ]);

        $user = User::where('email', 'pwdtest@test.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('Password1', $user->password));
        $this->assertNotEquals('Password1', $user->password);
    }

    public function test_registro_correo_duplicado(): void
    {
        $this->post(route('register'), [
            'nombre_completo' => 'Primero', 'email' => 'dup@test.com',
            'password' => 'Password1', 'password_confirmation' => 'Password1',
            'terminos' => '1',
        ]);

        // Logout so second attempt is as guest
        $this->post(route('logout'));

        $response = $this->post(route('register'), [
            'nombre_completo' => 'Segundo', 'email' => 'dup@test.com',
            'password' => 'Password2', 'password_confirmation' => 'Password2',
            'terminos' => '1',
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_cliente_existente_sin_user_se_vincula(): void
    {
        $cliente = Cliente::create([
            'nombre_completo' => 'Existente', 'email' => 'existente2@test.com',
            'telefono' => '70000002', 'fecha_registro' => now(), 'estado' => true,
        ]);

        $response = $this->post(route('register'), [
            'nombre_completo' => 'Existente', 'email' => 'existente2@test.com',
            'password' => 'Password1', 'password_confirmation' => 'Password1',
            'terminos' => '1',
        ]);

        $response->assertRedirect(route('cliente.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'existente2@test.com',
            'cliente_id' => $cliente->id,
        ]);
    }

    /* =============================================================
       GOOGLE
       ============================================================= */

    public function test_google_redirect(): void
    {
        $response = $this->get(route('auth.google.redirect'));
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location') ?? '');
    }

    public function test_google_redirect_funciona(): void
    {
        $response = $this->get(route('auth.google.redirect'));
        // Should redirect to Google accounts
        $this->assertStringContainsString('google', $response->headers->get('Location') ?? '');
    }

    /* =============================================================
       CLIENTE BLOQUEADO EN /ADMIN
       ============================================================= */

    public function test_cliente_no_accede_a_admin(): void
    {
        $this->post(route('register'), [
            'nombre_completo' => 'Test', 'email' => 'cliente_forbid@test.com',
            'password' => 'Password1', 'password_confirmation' => 'Password1',
            'terminos' => '1',
        ]);

        $response = $this->get(route('admin.sucursales.index'));
        $response->assertForbidden();
    }

    /* =============================================================
       REDIRECCIÓN SEGÚN ROL
       ============================================================= */

    public function test_redireccion_cliente_tras_registro(): void
    {
        $response = $this->post(route('register'), [
            'nombre_completo' => 'Test', 'email' => 'cliente@test.com',
            'password' => 'Password1', 'password_confirmation' => 'Password1',
            'terminos' => '1',
        ]);

        $response->assertRedirect(route('cliente.dashboard'));
        $this->assertAuthenticated();
    }
}
