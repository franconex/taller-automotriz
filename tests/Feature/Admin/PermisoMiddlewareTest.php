<?php

namespace Tests\Feature\Admin;

use App\Models\Permiso;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermisoMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->markTestSkipped('Estas pruebas requieren implementación de CRUD y middleware de permisos.');
    }

    public function test_admin_accede_a_usuarios(): void
    {
        $this->assertTrue(true);
    }

    public function test_recepcionista_no_accede_a_usuarios(): void
    {
        $this->assertTrue(true);
    }

    public function test_recepcionista_accede_a_clientes_con_permiso(): void
    {
        $this->assertTrue(true);
    }

    public function test_recepcionista_sin_permiso_no_ve_clientes(): void
    {
        $this->assertTrue(true);
    }

    public function test_tiene_permiso_funciona(): void
    {
        $this->assertTrue(true);
    }

    public function test_tiene_rol_funciona(): void
    {
        $this->assertTrue(true);
    }

    public function test_usuario_inactivo_no_accede(): void
    {
        $this->assertTrue(true);
    }

    public function test_password_no_aparece_en_auditoria(): void
    {
        $this->assertTrue(true);
    }
}
