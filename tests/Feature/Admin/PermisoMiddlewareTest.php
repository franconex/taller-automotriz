<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\PermisoMiddleware;
use App\Http\Middleware\RolMiddleware;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\RolPermisoSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PermisoMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $recepcionistaUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolSeeder::class,
            PermisoSeeder::class,
            RolPermisoSeeder::class,
        ]);

        $sucursal = Sucursal::create([
            'nombre' => 'Sucursal Test',
            'direccion' => 'Dir Test',
            'telefono' => '12345678',
            'horario_atencion' => 'Lun-Vie 8-18',
        ]);

        $createUser = fn (string $nombre, Rol $rol, string $estado = 'activo') => User::create([
            'nombre' => $nombre,
            'username' => strtolower($nombre),
            'email' => strtolower($nombre) . '@test.com',
            'password' => Hash::make('password'),
            'estado' => $estado,
            'rol_id' => $rol->id,
            'sucursal_id' => $sucursal->id,
        ]);

        $admin = Rol::where('nombre', 'Administrador')->firstOrFail();
        $recepcionista = Rol::where('nombre', 'Recepcionista')->firstOrFail();

        $this->adminUser = $createUser('admin', $admin);
        $this->recepcionistaUser = $createUser('recepcion', $recepcionista);
    }

    public function test_admin_accede_a_usuarios(): void
    {
        Auth::login($this->adminUser);

        $request = Request::create('/admin/usuarios', 'GET');
        $middleware = new PermisoMiddleware();
        $response = $middleware->handle($request, fn () => response('OK'), 'usuarios.ver');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_recepcionista_no_accede_a_usuarios(): void
    {
        Auth::login($this->recepcionistaUser);

        $request = Request::create('/admin/usuarios', 'GET');
        $middleware = new PermisoMiddleware();

        $this->expectException(HttpException::class);
        $middleware->handle($request, fn () => response('OK'), 'usuarios.ver');
    }

    public function test_recepcionista_accede_a_clientes_con_permiso(): void
    {
        Auth::login($this->recepcionistaUser);

        $request = Request::create('/recepcion/clientes', 'GET');
        $middleware = new PermisoMiddleware();
        $response = $middleware->handle($request, fn () => response('OK'), 'clientes.ver');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_recepcionista_sin_permiso_no_ve_roles(): void
    {
        Auth::login($this->recepcionistaUser);

        $request = Request::create('/admin/roles', 'GET');
        $middleware = new PermisoMiddleware();

        $this->expectException(HttpException::class);
        $middleware->handle($request, fn () => response('OK'), 'roles.ver');
    }

    public function test_tiene_permiso_funciona(): void
    {
        $this->assertTrue($this->adminUser->tienePermiso('usuarios.ver'));
        $this->assertTrue($this->recepcionistaUser->tienePermiso('clientes.ver'));
        $this->assertFalse($this->recepcionistaUser->tienePermiso('usuarios.ver'));
        $this->assertFalse($this->recepcionistaUser->tienePermiso('permisos.asignar'));
    }

    public function test_tiene_rol_funciona(): void
    {
        $this->assertTrue($this->adminUser->tieneRol('Administrador'));
        $this->assertFalse($this->recepcionistaUser->tieneRol('Administrador'));
        $this->assertTrue($this->recepcionistaUser->tieneRol('Recepcionista'));
    }

    public function test_usuario_inactivo_no_accede(): void
    {
        $this->adminUser->update(['estado' => 'inactivo']);
        Auth::login($this->adminUser);

        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new RolMiddleware();

        $this->expectException(HttpException::class);
        $middleware->handle($request, fn () => response('OK'), 'Administrador');
    }

    public function test_password_no_aparece_en_serializacion(): void
    {
        $this->assertArrayNotHasKey('password', $this->adminUser->toArray());
    }
}
