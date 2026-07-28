<?php

namespace Tests\Feature\Admin;

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

class SeguridadAccesosTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $recepcionUser;
    private Sucursal $sucursal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RolSeeder::class, PermisoSeeder::class, RolPermisoSeeder::class]);

        $this->sucursal = Sucursal::create([
            'nombre' => 'Suc Test', 'direccion' => 'Dir',
            'telefono' => '12345678', 'horario_atencion' => '8-18', 'estado' => true,
        ]);

        $adminRol = Rol::where('nombre', 'Administrador')->first();
        $recepRol = Rol::where('nombre', 'Recepcionista')->first();

        $this->adminUser = User::create([
            'sucursal_id' => $this->sucursal->id, 'rol_id' => $adminRol->id,
            'nombre' => 'Admin', 'username' => 'adminseg', 'email' => 'adminseg@test.com',
            'password' => Hash::make('password'), 'estado' => 'activo',
        ]);

        $this->recepcionUser = User::create([
            'sucursal_id' => $this->sucursal->id, 'rol_id' => $recepRol->id,
            'nombre' => 'Recep', 'username' => 'recepseg', 'email' => 'recepseg@test.com',
            'password' => Hash::make('password'), 'estado' => 'activo',
        ]);
    }

    /* =============================================================
       RECEPCIONISTA NO ACCEDE A RUTAS SIN PERMISO
       ============================================================= */

    public function test_recepcionista_no_ve_usuarios_sin_permiso(): void
    {
        $response = $this->actingAs($this->recepcionUser)->get(route('admin.usuarios.index'));
        $response->assertForbidden();
    }

    public function test_recepcionista_no_ve_roles_sin_permiso(): void
    {
        $response = $this->actingAs($this->recepcionUser)->get(route('admin.roles.index'));
        $response->assertForbidden();
    }

    public function test_recepcionista_ve_clientes_con_permiso(): void
    {
        $response = $this->actingAs($this->recepcionUser)->get(route('admin.clientes.index'));
        $response->assertOk();
    }

    /* =============================================================
       ADMIN ACCEDE A TODO
       ============================================================= */

    public function test_admin_ve_usuarios(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.usuarios.index'));
        $response->assertOk();
    }

    public function test_admin_ve_roles(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.roles.index'));
        $response->assertOk();
    }

    /* =============================================================
       CITA — UPDATE requiere citas.editar no solo citas.ver
       ============================================================= */

    public function test_cita_update_requiere_citas_editar(): void
    {
        $rolSinPermiso = Rol::firstOrCreate(['nombre' => 'SinPermiso'], ['estado' => true]);
        $userSinPermiso = User::create([
            'sucursal_id' => $this->sucursal->id, 'rol_id' => $rolSinPermiso->id,
            'nombre' => 'SinPermiso', 'username' => 'spcitas', 'email' => 'spcitas@test.com',
            'password' => Hash::make('password'), 'estado' => 'activo',
        ]);

        $cliente = Cliente::create([
            'nombre_completo' => 'Test', 'ci' => '11111111',
            'telefono' => '700000', 'fecha_registro' => now(), 'estado' => true,
        ]);
        $vehiculo = \App\Models\Vehiculo::create([
            'cliente_id' => $cliente->id,
            'placa' => 'TST111', 'anio' => 2020, 'estado' => true,
        ]);

        $cita = \App\Models\Cita::create([
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'sucursal_id' => $this->sucursal->id,
            'fecha' => now()->addDays(3)->format('Y-m-d'), 'hora' => '10:00',
            'estado' => 'solicitada', 'tipo' => 'mantenimiento',
            'descripcion_problema' => 'Test',
        ]);

        $response = $this->actingAs($userSinPermiso)
            ->putJson(route('admin.citas.update', $cita), [
                'cliente_id' => $cliente->id,
                'vehiculo_id' => $vehiculo->id,
                'sucursal_id' => $this->sucursal->id,
                'fecha' => now()->addDays(5)->format('Y-m-d'),
                'hora' => '14:00',
                'tipo' => 'reparacion',
                'descripcion_problema' => 'Test update',
            ]);

        $response->assertForbidden();
    }

    /* =============================================================
       VEHÍCULO — NO ACCEDER SIN PERMISO
       ============================================================= */

    public function test_sin_permiso_no_ve_vehiculos(): void
    {
        $userSinPermiso = User::create([
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => Rol::firstOrCreate(['nombre' => 'SinPermiso'], ['estado' => true])->id,
            'nombre' => 'SinPermiso', 'username' => 'sinpermiso',
            'email' => 'sinpermiso@test.com', 'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($userSinPermiso)->get(route('admin.vehiculos.index'));
        $response->assertForbidden();
    }

    /* =============================================================
       MECÁNICO SIN ASIGNACIÓN NO ACCEDE A ORDEN AJENA
       ============================================================= */

    public function test_mecanico_sin_asignacion_no_ve_orden_ajena(): void
    {
        $mecRol = Rol::where('nombre', 'Mecánico')->first();
        $empleado = \App\Models\Empleado::create([
            'sucursal_id' => $this->sucursal->id, 'rol_id' => $mecRol->id,
            'nombre_completo' => 'Mec', 'ci' => '55555555', 'telefono' => '700055', 'estado' => true,
        ]);

        // Create mecánico but WITHOUT any user account (shouldn't be possible but tests edge case)
        $userMec = User::create([
            'empleado_id' => $empleado->id, 'sucursal_id' => $this->sucursal->id, 'rol_id' => $mecRol->id,
            'nombre' => 'Mec', 'username' => 'mecseg', 'email' => 'mecseg@test.com',
            'password' => Hash::make('password'), 'estado' => 'activo',
        ]);

        $orden = \App\Models\OrdenTrabajo::create([
            'numero_orden' => 'OT-SEG-001',
            'cliente_id' => Cliente::create([
                'nombre_completo' => 'Cliente', 'ci' => '66666666',
                'telefono' => '700066', 'fecha_registro' => now(), 'estado' => true,
            ])->id,
            'vehiculo_id' => \App\Models\Vehiculo::create([
                'cliente_id' => Cliente::where('ci', '66666666')->first()->id,
                'placa' => 'SEG001', 'anio' => 2021, 'estado' => true,
            ])->id,
            'sucursal_id' => $this->sucursal->id, 'estado' => 'recibida',
            'fecha_emision' => now(), 'descripcion_problema' => 'Test',
        ]);

        $response = $this->actingAs($userMec)
            ->get(route('admin.ordenes.show', $orden));

        $response->assertForbidden();
    }
}
