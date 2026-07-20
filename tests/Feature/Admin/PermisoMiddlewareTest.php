<?php

namespace Tests\Feature\Admin;

use App\Models\Auditoria;
use App\Models\permisos;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermisoMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $recepcionista;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRol = Rol::factory()->create(['nombre' => 'Administrador']);
        $adminPermisos = permisos::pluck('id')->toArray();
        $adminRol->permisos()->sync($adminPermisos);

        $recepRol = Rol::factory()->create(['nombre' => 'Recepcionista']);

        $this->admin = User::factory()->create([
            'rol_id' => $adminRol->id,
            'estado' => true,
        ]);

        $this->recepcionista = User::factory()->create([
            'rol_id' => $recepRol->id,
            'estado' => true,
        ]);
    }

    public function test_admin_accede_a_usuarios()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.usuarios.index'));
        $response->assertOk();
    }

    public function test_recepcionista_no_accede_a_usuarios()
    {
        $response = $this->actingAs($this->recepcionista)->get(route('admin.usuarios.index'));
        $response->assertStatus(403);
    }

    public function test_recepcionista_accede_a_clientes_con_permiso()
    {
        $permiso = permisos::firstOrCreate(
            ['codigo' => 'clientes.ver'],
            ['nombre' => 'Ver clientes', 'modulo' => 'Clientes']
        );

        $this->recepcionista->rol->permisos()->sync([$permiso->id]);
        $this->recepcionista->load('rol.permisos');

        $response = $this->actingAs($this->recepcionista)->get(route('admin.clientes.index'));
        $response->assertOk();
    }

    public function test_recepcionista_sin_permiso_no_ve_clientes()
    {
        $response = $this->actingAs($this->recepcionista)->get(route('admin.clientes.index'));
        $response->assertStatus(403);
    }

    public function test_tiene_permiso_funciona()
    {
        $permiso = permisos::firstOrCreate(
            ['codigo' => 'clientes.ver'],
            ['nombre' => 'Ver clientes', 'modulo' => 'Clientes']
        );

        $this->assertFalse($this->recepcionista->tienePermiso('clientes.ver'));

        $this->recepcionista->rol->permisos()->sync([$permiso->id]);
        $this->recepcionista->load('rol.permisos');

        $this->assertTrue($this->recepcionista->tienePermiso('clientes.ver'));
        $this->assertFalse($this->recepcionista->tienePermiso('usuarios.crear'));
    }

    public function test_tiene_rol_funciona()
    {
        $this->assertTrue($this->admin->tieneRol('Administrador'));
        $this->assertFalse($this->admin->tieneRol('Recepcionista'));
        $this->assertTrue($this->recepcionista->tieneRol('Recepcionista'));
    }

    public function test_usuario_inactivo_no_accede()
    {
        $this->admin->update(['estado' => false]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_password_no_aparece_en_auditoria()
    {
        $user = User::factory()->create(['rol_id' => $this->admin->rol_id]);

        $this->actingAs($this->admin)->put(route('admin.usuarios.update', $user), [
            'rol_id' => $this->admin->rol_id,
            'nombre' => 'Test',
            'username' => $user->username,
            'email' => $user->email,
        ]);

        $auditoria = Auditoria::where('entidad_afectada', 'Usuario')
            ->latest()
            ->first();

        if ($auditoria) {
            $valores = $auditoria->valores_nuevos ?? [];
            $this->assertArrayNotHasKey('password', $valores);
        }
    }
}
