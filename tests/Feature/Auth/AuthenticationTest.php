<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $gerenteUser;
    private User $recepcionistaUser;
    private User $mecanicoUser;
    private Sucursal $sucursal;

    protected function setUp(): void
    {
        parent::setUp();

        $roles = [
            'Administrador' => Role::create(['nombre' => 'Administrador']),
            'Gerente' => Role::create(['nombre' => 'Gerente']),
            'Recepcionista' => Role::create(['nombre' => 'Recepcionista']),
            'Mecánico' => Role::create(['nombre' => 'Mecánico']),
        ];

        $this->sucursal = Sucursal::create([
            'nombre' => 'Sucursal Test',
            'direccion' => 'Dir Test',
            'telefono' => '12345678',
            'horario_atencion' => 'Lun-Vie 8-18',
        ]);

        $createUser = fn(string $nombre, string $username, string $email, Role $rol, string $estado = 'activo') =>
            User::create([
                'nombre' => $nombre,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password'),
                'estado' => $estado,
                'rol_id' => $rol->id,
                'sucursal_id' => $this->sucursal->id,
            ]);

        $this->adminUser = $createUser('Admin', 'admin', 'admin@test.com', $roles['Administrador']);
        $this->gerenteUser = $createUser('Gerente', 'gerente', 'gerente@test.com', $roles['Gerente']);
        $this->recepcionistaUser = $createUser('Recepcion', 'recepcion', 'recepcion@test.com', $roles['Recepcionista']);
        $this->mecanicoUser = $createUser('Mecanico', 'mecanico', 'mecanico@test.com', $roles['Mecánico']);
    }

    public function test_muestra_formulario_login(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Ingresar al sistema');
        $response->assertSee('Acceso al personal');
        $response->assertSee('login');
        $response->assertSee('password');
    }

    public function test_login_con_email_correcto(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_con_username_correcto(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_rechaza_password_incorrecto(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_rechaza_usuario_inactivo(): void
    {
        $this->adminUser->update(['estado' => 'inactivo']);

        $response = $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_rechaza_usuario_con_sucursal_inactiva(): void
    {
        $this->sucursal->update(['estado' => false]);

        $response = $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_redirige_segun_rol_admin(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_redirige_segun_rol_gerente(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'gerente@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/gerente/dashboard');
    }

    public function test_redirige_segun_rol_recepcionista(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'recepcion@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/recepcion/dashboard');
    }

    public function test_redirige_segun_rol_mecanico(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'mecanico@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/mecanico/dashboard');
    }

    public function test_cierra_sesion_con_post(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->post(route('logout'));

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_no_permite_logout_con_get(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/logout');

        $this->assertEquals(405, $response->status());
    }

    public function test_impide_acceso_anonimo_a_paneles(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_actualiza_ultimo_acceso(): void
    {
        $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $this->adminUser->refresh();
        $this->assertNotNull($this->adminUser->ultimo_acceso);
    }

    public function test_mantiene_intended_desde_ruta_protegida(): void
    {
        $this->get(route('admin.dashboard'));

        $response = $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_aplica_limitacion_intentos(): void
    {
        $uniqueLogin = 'throttle-test@test.com';
        User::create([
            'nombre' => 'Throttle',
            'username' => 'throttle',
            'email' => $uniqueLogin,
            'password' => Hash::make('password'),
            'estado' => 'activo',
            'rol_id' => $this->adminUser->rol_id,
            'sucursal_id' => $this->sucursal->id,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'login' => $uniqueLogin,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('login'), [
            'login' => $uniqueLogin,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_mensaje_generico_sin_revelar_cuenta_inactiva(): void
    {
        $this->adminUser->update(['estado' => 'inactivo']);

        $response = $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login');
        $response->assertSessionDoesntHaveErrors('email');
    }

    public function test_no_hay_ruta_register(): void
    {
        $response = $this->get('/register');

        $this->assertEquals(404, $response->status());
    }
}
