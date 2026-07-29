<?php

namespace Tests\Feature\Cliente;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\TipoServicio;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\MarcaVehiculo;
use App\Models\ModeloVehiculo;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\RolPermisoSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CitaSolicitudTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $cliente;
    private User $user;
    private Vehiculo $vehiculo;
    private Sucursal $sucursal;
    private Servicio $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RolSeeder::class, PermisoSeeder::class, RolPermisoSeeder::class]);

        $rolCliente = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);

        $this->cliente = Cliente::create([
            'nombre_completo' => 'Cliente Test',
            'ci' => '11111111', 'telefono' => '70000001',
            'fecha_registro' => now(), 'estado' => true,
        ]);

        $this->user = User::create([
            'cliente_id' => $this->cliente->id,
            'rol_id' => $rolCliente->id,
            'nombre' => 'Cliente Test',
            'username' => 'clientetest',
            'email' => 'ct@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $marca = MarcaVehiculo::create(['nombre' => 'Toyota', 'estado' => true]);
        $modelo = ModeloVehiculo::create(['marca_vehiculo_id' => $marca->id, 'nombre' => 'Corolla', 'estado' => true]);

        $this->vehiculo = Vehiculo::create([
            'cliente_id' => $this->cliente->id,
            'modelo_vehiculo_id' => $modelo->id,
            'placa' => 'ABC123', 'anio' => 2020, 'estado' => true,
        ]);

        $this->sucursal = Sucursal::create([
            'nombre' => 'Sucursal Test', 'direccion' => 'Dir',
            'telefono' => '12345678', 'horario_atencion' => 'Lun-Vie 8-18', 'estado' => true,
        ]);

        $tipoServicio = TipoServicio::create(['nombre' => 'Mantenimiento', 'estado' => true]);
        $this->servicio = Servicio::create([
            'tipo_servicio_id' => $tipoServicio->id,
            'nombre' => 'Cambio de aceite',
            'precio_base' => 100, 'estado' => true,
        ]);
    }

    private function datosValidos(): array
    {
        return [
            'vehiculo_id' => $this->vehiculo->id,
            'servicio_id' => $this->servicio->id,
            'sucursal_id' => $this->sucursal->id,
            'fecha' => now()->addDays(3)->format('Y-m-d'),
            'hora' => '10:00',
            'tipo' => 'mantenimiento',
            'descripcion_problema' => 'Cambio de aceite y filtro',
            'deja_vehiculo' => false,
            'observaciones' => 'Llegaré a las 9:45',
        ];
    }

    /* =============================================================
       FORMULARIO
       ============================================================= */

    public function test_cliente_ve_formulario_de_cita(): void
    {
        $response = $this->actingAs($this->user)->get(route('cliente.citas.crear'));

        $response->assertOk();
        $response->assertSee('Solicitar cita');
        $response->assertSee('ABC123');
        $response->assertSee($this->sucursal->nombre);
    }

    /* =============================================================
       CREACIÓN
       ============================================================= */

    public function test_cliente_solicita_cita(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('cliente.citas.store'), $this->datosValidos());

        $response->assertRedirect(route('cliente.citas'));

        $this->assertDatabaseHas('citas', [
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'sucursal_id' => $this->sucursal->id,
            'estado' => 'solicitada',
        ]);
    }

    public function test_cita_se_crea_como_solicitada(): void
    {
        $this->actingAs($this->user)->post(route('cliente.citas.store'), $this->datosValidos());

        $cita = Cita::where('cliente_id', $this->cliente->id)->first();
        $this->assertEquals('solicitada', $cita->estado);
    }

    /* =============================================================
       SEGURIDAD — CLIENTE
       ============================================================= */

    public function test_no_puede_usar_vehiculo_de_otro(): void
    {
        $otroCliente = Cliente::create([
            'nombre_completo' => 'Otro', 'ci' => '99999999',
            'telefono' => '70000099', 'fecha_registro' => now(), 'estado' => true,
        ]);
        $marca = MarcaVehiculo::create(['nombre' => 'Honda', 'estado' => true]);
        $modelo = ModeloVehiculo::create(['marca_vehiculo_id' => $marca->id, 'nombre' => 'Civic', 'estado' => true]);
        $otroVehiculo = Vehiculo::create([
            'cliente_id' => $otroCliente->id,
            'modelo_vehiculo_id' => $modelo->id,
            'placa' => 'ZZZ999', 'anio' => 2021, 'estado' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('cliente.citas.store'), array_merge($this->datosValidos(), [
                'vehiculo_id' => $otroVehiculo->id,
            ]));

        $response->assertSessionHasErrors('vehiculo_id');
    }

    /* =============================================================
       VALIDACIONES
       ============================================================= */

    public function test_rechaza_fecha_pasada(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('cliente.citas.store'), array_merge($this->datosValidos(), [
                'fecha' => now()->subDays(1)->format('Y-m-d'),
            ]));

        $response->assertSessionHasErrors('fecha');
    }

    public function test_rechaza_campos_requeridos(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('cliente.citas.store'), []);

        $response->assertSessionHasErrors(['vehiculo_id', 'sucursal_id', 'fecha', 'hora', 'tipo']);
    }

    public function test_rechaza_choque_horario(): void
    {
        Cita::create([
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'sucursal_id' => $this->sucursal->id,
            'fecha' => now()->addDays(3)->format('Y-m-d'),
            'hora' => '10:00',
            'hora_fin' => '11:00',
            'estado' => 'confirmada',
            'tipo' => 'mantenimiento',
            'descripcion_problema' => 'Test',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('cliente.citas.store'), $this->datosValidos());

        $response->assertSessionHasErrors('vehiculo_id');
    }

    /* =============================================================
       CANCELACIÓN
       ============================================================= */

    public function test_cliente_cancela_su_cita(): void
    {
        $cita = Cita::create([
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'sucursal_id' => $this->sucursal->id,
            'fecha' => now()->addDays(3)->format('Y-m-d'),
            'hora' => '10:00', 'estado' => 'solicitada', 'tipo' => 'mantenimiento',
            'descripcion_problema' => 'Test',
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('cliente.citas.cancelar', $cita));

        $response->assertRedirect(route('cliente.citas'));
        $this->assertEquals('cancelada', $cita->fresh()->estado);
    }

    public function test_cliente_no_cancela_cita_de_otro(): void
    {
        $otroCliente = Cliente::create([
            'nombre_completo' => 'Otro', 'ci' => '88888888',
            'telefono' => '70000088', 'fecha_registro' => now(), 'estado' => true,
        ]);
        $marca = MarcaVehiculo::create(['nombre' => 'Nissan', 'estado' => true]);
        $modelo = ModeloVehiculo::create(['marca_vehiculo_id' => $marca->id, 'nombre' => 'Sentra', 'estado' => true]);
        $otroV = Vehiculo::create([
            'cliente_id' => $otroCliente->id,
            'modelo_vehiculo_id' => $modelo->id,
            'placa' => 'YYY888', 'anio' => 2022, 'estado' => true,
        ]);
        $cita = Cita::create([
            'cliente_id' => $otroCliente->id,
            'vehiculo_id' => $otroV->id,
            'sucursal_id' => $this->sucursal->id,
            'fecha' => now()->addDays(5)->format('Y-m-d'),
            'hora' => '14:00', 'estado' => 'solicitada', 'tipo' => 'reparacion',
            'descripcion_problema' => 'Test',
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('cliente.citas.cancelar', $cita));

        $response->assertForbidden();
    }

    /* =============================================================
       ADMIN — PROPOSER / RECHAZAR
       ============================================================= */

    public function test_admin_puede_rechazar_cita_solicitada(): void
    {
        $adminRol = Rol::where('nombre', 'Administrador')->first();
        $admin = User::create([
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => $adminRol->id,
            'nombre' => 'Admin', 'username' => 'admincitas',
            'email' => 'admincitas@test.com', 'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $cita = Cita::create([
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'sucursal_id' => $this->sucursal->id,
            'fecha' => now()->addDays(3)->format('Y-m-d'),
            'hora' => '10:00', 'estado' => 'solicitada', 'tipo' => 'mantenimiento',
            'descripcion_problema' => 'Test',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson(route('admin.citas.cancelar', $cita), [
                'cancelado_motivo' => 'No tenemos disponibilidad esa fecha',
            ]);

        $response->assertOk();
        $this->assertEquals('cancelada', $cita->fresh()->estado);
    }

    public function test_admin_puede_proponer_nuevo_horario(): void
    {
        $adminRol = Rol::where('nombre', 'Administrador')->first();
        $admin = User::create([
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => $adminRol->id,
            'nombre' => 'Admin', 'username' => 'adminprop',
            'email' => 'adminprop@test.com', 'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $cita = Cita::create([
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'sucursal_id' => $this->sucursal->id,
            'fecha' => now()->addDays(3)->format('Y-m-d'),
            'hora' => '10:00', 'estado' => 'solicitada', 'tipo' => 'mantenimiento',
            'descripcion_problema' => 'Test',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson(route('admin.citas.proponer', $cita), [
                'fecha' => now()->addDays(5)->format('Y-m-d'),
                'hora' => '15:00',
                'sucursal_id' => $this->sucursal->id,
                'motivo_reprogramacion' => 'Propuesta: mejor horario disponible',
            ]);

        $response->assertOk();
        $this->assertEquals('propuesta', $cita->fresh()->estado);
    }
}
