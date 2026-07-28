<?php

namespace Tests\Feature\Cliente;

use App\Models\AsignacionTrabajo;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\MarcaVehiculo;
use App\Models\Mecanico;
use App\Models\ModeloVehiculo;
use App\Models\NotaTrabajo;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\MetodoPago;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Vehiculo;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\RolPermisoSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PortalClienteTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $clienteA;
    private Cliente $clienteB;
    private User $userA;
    private User $userB;
    private Vehiculo $vehiculoA;
    private Vehiculo $vehiculoB;
    private OrdenTrabajo $ordenA;
    private OrdenTrabajo $ordenB;
    private Cita $citaA;
    private Cita $citaB;
    private Pago $pagoA;
    private Comprobante $comprobanteA;

    private ?Mecanico $mecanico = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RolSeeder::class, PermisoSeeder::class, RolPermisoSeeder::class]);

        $rolCliente = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);
        $rolMecanico = Rol::where('nombre', 'Mecánico')->first();
        $sucursal = Sucursal::create([
            'nombre' => 'Sucursal Test', 'direccion' => 'Dir',
            'telefono' => '12345678', 'horario_atencion' => 'Lun-Vie 8-18', 'estado' => true,
        ]);

        $this->clienteA = Cliente::create([
            'nombre_completo' => 'Cliente A',
            'ci' => '11111111', 'telefono' => '70000001',
            'fecha_registro' => now(), 'estado' => true,
        ]);
        $this->clienteB = Cliente::create([
            'nombre_completo' => 'Cliente B',
            'ci' => '22222222', 'telefono' => '70000002',
            'fecha_registro' => now(), 'estado' => true,
        ]);

        $this->userA = User::create([
            'cliente_id' => $this->clienteA->id,
            'rol_id' => $rolCliente->id,
            'nombre' => 'Cliente A', 'username' => 'clientea',
            'email' => 'clientea@test.com', 'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);
        $this->userB = User::create([
            'cliente_id' => $this->clienteB->id,
            'rol_id' => $rolCliente->id,
            'nombre' => 'Cliente B', 'username' => 'clienteb',
            'email' => 'clienteb@test.com', 'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $marca = MarcaVehiculo::create(['nombre' => 'Toyota', 'estado' => true]);
        $modelo = ModeloVehiculo::create(['marca_vehiculo_id' => $marca->id, 'nombre' => 'Corolla', 'estado' => true]);

        $this->vehiculoA = Vehiculo::create([
            'cliente_id' => $this->clienteA->id,
            'modelo_vehiculo_id' => $modelo->id,
            'placa' => 'AAA111', 'anio' => 2020, 'estado' => true,
        ]);
        $this->vehiculoB = Vehiculo::create([
            'cliente_id' => $this->clienteB->id,
            'modelo_vehiculo_id' => $modelo->id,
            'placa' => 'BBB222', 'anio' => 2021, 'estado' => true,
        ]);

        $this->citaA = Cita::create([
            'cliente_id' => $this->clienteA->id,
            'vehiculo_id' => $this->vehiculoA->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => now()->addDays(3), 'hora' => '10:00',
            'estado' => 'pendiente', 'tipo' => 'mantenimiento',
            'descripcion_problema' => 'Ruido en motor',
        ]);
        $this->citaB = Cita::create([
            'cliente_id' => $this->clienteB->id,
            'vehiculo_id' => $this->vehiculoB->id,
            'sucursal_id' => $sucursal->id,
            'fecha' => now()->addDays(5), 'hora' => '14:00',
            'estado' => 'confirmada', 'tipo' => 'reparación',
            'descripcion_problema' => 'Frenos delanteros',
        ]);

        $this->ordenA = OrdenTrabajo::create([
            'numero_orden' => 'OT-CA-001',
            'cliente_id' => $this->clienteA->id,
            'vehiculo_id' => $this->vehiculoA->id,
            'sucursal_id' => $sucursal->id, 'estado' => 'recibida',
            'fecha_emision' => now(), 'descripcion_problema' => 'Ruido motor',
            'total_general' => 500,
        ]);
        $this->ordenB = OrdenTrabajo::create([
            'numero_orden' => 'OT-CB-002',
            'cliente_id' => $this->clienteB->id,
            'vehiculo_id' => $this->vehiculoB->id,
            'sucursal_id' => $sucursal->id, 'estado' => 'finalizada',
            'fecha_emision' => now()->subDays(10), 'descripcion_problema' => 'Frenos',
            'total_general' => 300,
        ]);

        $empleadoMec = Empleado::create([
            'sucursal_id' => $sucursal->id,
            'rol_id' => $rolMecanico->id,
            'nombre_completo' => 'Mecánico Test',
            'ci' => '33333333', 'telefono' => '70000003', 'estado' => true,
        ]);
        $esp = Especialidad::create(['nombre' => 'General', 'estado' => true]);
        $this->mecanico = Mecanico::create([
            'empleado_id' => $empleadoMec->id,
            'especialidad_id' => $esp->id,
            'disponibilidad' => 'disponible',
        ]);

        $metodo = MetodoPago::create(['nombre' => 'Efectivo', 'estado' => true]);
        $this->pagoA = Pago::create([
            'orden_trabajo_id' => $this->ordenA->id,
            'metodo_pago_id' => $metodo->id,
            'usuario_id' => $this->userA->id,
            'fecha_pago' => now(), 'monto' => 500,
            'numero_comprobante' => 'PAG-001', 'estado' => 'confirmado',
        ]);
        $this->comprobanteA = Comprobante::create([
            'pago_id' => $this->pagoA->id,
            'cliente_id' => $this->clienteA->id,
            'numero' => 'COMP-001',
            'fecha_emision' => now(), 'monto_total' => 500,
            'nit_ci' => '11111111', 'razon_social' => 'Cliente A',
            'estado' => 'emitido',
        ]);
    }

    /* =============================================================
       DASHBOARD
       ============================================================= */

    public function test_cliente_ve_su_dashboard(): void
    {
        $response = $this->actingAs($this->userA)->get(route('cliente.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Hola');
    }

    /* =============================================================
       VEHÍCULOS — aislamiento
       ============================================================= */

    public function test_cliente_ve_sus_vehiculos(): void
    {
        $response = $this->actingAs($this->userA)->get(route('cliente.vehiculos'));

        $response->assertOk();
        $response->assertSee('AAA111');
        $response->assertDontSee('BBB222');
    }

    public function test_cliente_no_ve_vehiculo_de_otro(): void
    {
        $response = $this->actingAs($this->userA)
            ->get(route('cliente.vehiculo-show', $this->vehiculoB));

        $response->assertForbidden();
    }

    public function test_cliente_ve_su_vehiculo(): void
    {
        $response = $this->actingAs($this->userA)
            ->get(route('cliente.vehiculo-show', $this->vehiculoA));

        $response->assertOk();
        $response->assertSee('AAA111');
    }

    /* =============================================================
       CITAS — aislamiento
       ============================================================= */

    public function test_cliente_ve_sus_citas(): void
    {
        $response = $this->actingAs($this->userA)->get(route('cliente.citas'));

        $response->assertOk();
        $response->assertSee('mantenimiento');
        $response->assertDontSee('reparación');
    }

    public function test_cliente_no_ve_cita_de_otro(): void
    {
        $response = $this->actingAs($this->userA)
            ->get(route('cliente.cita-show', $this->citaB));

        $response->assertForbidden();
    }

    /* =============================================================
       SEGUIMIENTO
       ============================================================= */

    public function test_cliente_ve_seguimiento_de_su_orden_activa(): void
    {
        $response = $this->actingAs($this->userA)->get(route('cliente.seguimiento'));

        $response->assertOk();
        $response->assertSee('OT-CA-001');
        $response->assertSee('Ruido motor');
    }

    /* =============================================================
       HISTORIAL — aislamiento
       ============================================================= */

    public function test_cliente_ve_su_historial(): void
    {
        $response = $this->actingAs($this->userB)->get(route('cliente.historial'));

        $response->assertOk();
        $response->assertSee('OT-CB-002');
        $response->assertDontSee('OT-CA-001');
    }

    public function test_cliente_no_ve_orden_de_otro(): void
    {
        $response = $this->actingAs($this->userA)
            ->get(route('cliente.orden-show', $this->ordenB));

        $response->assertForbidden();
    }

    /* =============================================================
       PAGOS — aislamiento
       ============================================================= */

    public function test_cliente_ve_sus_pagos(): void
    {
        $response = $this->actingAs($this->userA)->get(route('cliente.pagos'));

        $response->assertOk();
        $response->assertSee('OT-CA-001');
        $response->assertSee('Comprobante');
    }

    public function test_cliente_no_ve_pago_de_otro(): void
    {
        $pagoB = Pago::create([
            'orden_trabajo_id' => $this->ordenB->id,
            'metodo_pago_id' => MetodoPago::first()->id,
            'usuario_id' => $this->userB->id,
            'fecha_pago' => now(), 'monto' => 300,
            'numero_comprobante' => 'PAG-002', 'estado' => 'confirmado',
        ]);

        $response = $this->actingAs($this->userA)
            ->get(route('cliente.pago-show', $pagoB));

        $response->assertForbidden();
    }

    /* =============================================================
       COMPROBANTE — aislamiento
       ============================================================= */

    public function test_cliente_no_ve_comprobante_de_otro(): void
    {
        $comprobanteB = Comprobante::create([
            'pago_id' => Pago::create([
                'orden_trabajo_id' => $this->ordenB->id,
                'metodo_pago_id' => MetodoPago::first()->id,
                'usuario_id' => $this->userB->id,
                'fecha_pago' => now(), 'monto' => 300,
                'numero_comprobante' => 'PAG-003', 'estado' => 'confirmado',
            ])->id,
            'cliente_id' => $this->clienteB->id,
            'numero' => 'COMP-002',
            'fecha_emision' => now(), 'monto_total' => 300,
            'nit_ci' => '22222222', 'razon_social' => 'Cliente B',
            'estado' => 'emitido',
        ]);

        $response = $this->actingAs($this->userA)
            ->get(route('cliente.comprobante-show', $comprobanteB));

        $response->assertForbidden();
    }

    /* =============================================================
       PERFIL
       ============================================================= */

    public function test_cliente_ve_su_perfil(): void
    {
        $response = $this->actingAs($this->userA)->get(route('cliente.perfil'));

        $response->assertOk();
        $response->assertSee('Cliente A');
        $response->assertSee('clientea@test.com');
    }

    public function test_cliente_actualiza_perfil(): void
    {
        $response = $this->actingAs($this->userA)
            ->put(route('cliente.perfil.update'), [
                'telefono' => '77777777',
                'direccion' => 'Nueva dirección 123',
            ]);

        $response->assertRedirect(route('cliente.perfil'));

        $this->clienteA->refresh();
        $this->assertEquals('77777777', $this->clienteA->telefono);
        $this->assertEquals('Nueva dirección 123', $this->clienteA->direccion);
    }

    /* =============================================================
       SEGURIDAD — NO EXPONER
       ============================================================= */

    public function test_cliente_no_ve_notas_internas_en_seguimiento(): void
    {
        $asignacion = AsignacionTrabajo::create([
            'orden_trabajo_id' => $this->ordenA->id,
            'mecanico_id' => $this->mecanico->id,
            'usuario_asignador_id' => $this->userA->id,
            'actividad_asignada' => 'Revisión', 'prioridad' => 'normal',
            'estado' => 'activa', 'fecha_asignacion' => now(),
        ]);

        NotaTrabajo::create([
            'asignacion_trabajo_id' => $asignacion->id,
            'usuario_id' => $this->userA->id,
            'contenido' => 'Nota INTERNA — no visible',
            'visible_cliente' => false,
        ]);
        NotaTrabajo::create([
            'asignacion_trabajo_id' => $asignacion->id,
            'usuario_id' => $this->userA->id,
            'contenido' => 'Nota VISIBLE — ok',
            'visible_cliente' => true,
        ]);

        $response = $this->actingAs($this->userA)->get(route('cliente.seguimiento'));

        $response->assertOk();
        $response->assertSee('Nota VISIBLE');
        $response->assertDontSee('Nota INTERNA');
    }

    public function test_cliente_no_accede_a_ruta_admin(): void
    {
        $response = $this->actingAs($this->userA)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }
}
