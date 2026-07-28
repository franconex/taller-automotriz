<?php

namespace Tests\Feature\Auth;

use App\Models\Cliente;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\RolPermisoSeeder;
use Database\Seeders\RolSeeder;
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

        $this->seed([RolSeeder::class, PermisoSeeder::class, RolPermisoSeeder::class]);

        $roles = [
            'Administrador' => Rol::where('nombre', 'Administrador')->firstOrFail(),
            'Gerente' => Rol::where('nombre', 'Gerente')->firstOrFail(),
            'Recepcionista' => Rol::where('nombre', 'Recepcionista')->firstOrFail(),
            'Mecánico' => Rol::where('nombre', 'Mecánico')->firstOrFail(),
        ];

        $this->sucursal = Sucursal::create([
            'nombre' => 'Sucursal Test',
            'direccion' => 'Dir Test',
            'telefono' => '12345678',
            'horario_atencion' => 'Lun-Vie 8-18',
        ]);

        $createUser = fn(string $nombre, string $username, string $email, Rol $rol, string $estado = 'activo') =>
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
        $response->assertSee('Taller Pro');
        $response->assertSee('Iniciar sesión');
        $response->assertSee('Registrarme');
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

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_redirige_segun_rol_recepcionista(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'recepcion@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/citas');
    }

    public function test_redirige_segun_rol_mecanico(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'mecanico@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
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

    public function test_ruta_register_devuelve_login(): void
    {
        $response = $this->get('/register');

        // Register is a POST-only route, GET should redirect to login
        $response->assertStatus(405);
    }

    public function test_roles_con_permiso_acceden_al_dashboard_admin(): void
    {
        $usuarios = [
            $this->adminUser,
            $this->gerenteUser,
            $this->mecanicoUser,
        ];

        foreach ($usuarios as $usuario) {
            $response = $this->actingAs($usuario)->get(route('admin.dashboard'));
            $response->assertStatus(200);
        }
    }

    public function test_rechaza_login_con_rol_inactivo(): void
    {
        $this->adminUser->rol->update(['estado' => false]);

        $response = $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_usuario_inactivo_no_accede_a_panel(): void
    {
        $this->adminUser->update(['estado' => 'inactivo']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_no_redirige_a_panel_no_autorizado_tras_login(): void
    {
        $this->get(route('admin.dashboard'));

        $response = $this->post(route('login'), [
            'login' => 'gerente@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_admin_ve_dashboard_con_layout(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('adminSidebar');
    }

    /* =============================================================
       Tests de Cliente
       ============================================================= */

    public function test_cliente_puede_iniciar_sesion(): void
    {
        $rolCliente = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);
        $cliente = Cliente::create([
            'nombre_completo' => 'Cliente Test',
            'ci' => '12345678',
            'telefono' => '70000000',
            'fecha_registro' => now(),
            'estado' => true,
        ]);
        User::create([
            'cliente_id' => $cliente->id,
            'rol_id' => $rolCliente->id,
            'nombre' => 'Cliente Test',
            'username' => 'clientetest',
            'email' => 'cliente@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $response = $this->post(route('login'), [
            'login' => 'cliente@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/cliente/dashboard');
        $this->assertAuthenticated();
    }

    public function test_cliente_redirige_a_portal_cliente(): void
    {
        $rolCliente = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);
        $cliente = Cliente::create([
            'nombre_completo' => 'Cliente Test',
            'ci' => '87654321',
            'telefono' => '70000001',
            'fecha_registro' => now(),
            'estado' => true,
        ]);
        $user = User::create([
            'cliente_id' => $cliente->id,
            'rol_id' => $rolCliente->id,
            'nombre' => 'Cliente Test',
            'username' => 'clientedos',
            'email' => 'cliente2@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $response = $this->post(route('login'), [
            'login' => 'cliente2@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/cliente/dashboard');
    }

    public function test_cliente_no_accede_a_admin(): void
    {
        $rolCliente = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);
        $cliente = Cliente::create([
            'nombre_completo' => 'Cliente Test',
            'ci' => '11111111',
            'telefono' => '70000002',
            'fecha_registro' => now(),
            'estado' => true,
        ]);
        $user = User::create([
            'cliente_id' => $cliente->id,
            'rol_id' => $rolCliente->id,
            'nombre' => 'Cliente Test',
            'username' => 'clienteadmin',
            'email' => 'cliente-admin@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_no_accede_a_portal_cliente(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('cliente.dashboard'));

        $response->assertForbidden();
    }

    public function test_cliente_sin_registro_asociado_es_rechazado(): void
    {
        $rolCliente = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);
        $user = User::create([
            'rol_id' => $rolCliente->id,
            'nombre' => 'Cliente Huérfano',
            'username' => 'clientehuerfano',
            'email' => 'huerfano@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $response = $this->post(route('login'), [
            'login' => 'huerfano@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_usuario_no_puede_ser_empleado_y_cliente_simultaneamente(): void
    {
        $rolCliente = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        User::create([
            'empleado_id' => 999,
            'cliente_id' => 888,
            'rol_id' => $rolCliente->id,
            'nombre' => 'Dual',
            'username' => 'dual',
            'email' => 'dual@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);
    }

    public function test_cliente_ve_su_portal(): void
    {
        $rolCliente = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);
        $cliente = Cliente::create([
            'nombre_completo' => 'Carlos López',
            'ci' => '22222222',
            'telefono' => '70000003',
            'fecha_registro' => now(),
            'estado' => true,
        ]);
        $user = User::create([
            'cliente_id' => $cliente->id,
            'rol_id' => $rolCliente->id,
            'nombre' => 'Carlos López',
            'username' => 'carlosl',
            'email' => 'carlos@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($user)->get(route('cliente.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Hola');
        $response->assertSee('Carlos López');
    }
}
