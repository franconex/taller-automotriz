<?php

namespace Tests\Feature\Admin;

use App\Models\Autorizacion;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\MarcaVehiculo;
use App\Models\Mecanico;
use App\Models\ModeloVehiculo;
use App\Models\OrdenTrabajo;
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

class AutorizacionTest extends TestCase
{
    use RefreshDatabase;

    private User $mecanicoUser;
    private User $adminUser;
    private User $clienteUser;
    private Cliente $cliente;
    private OrdenTrabajo $orden;
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
        $mecRol = Rol::where('nombre', 'Mecánico')->first();
        $cliRol = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);

        $this->adminUser = User::create([
            'sucursal_id' => $this->sucursal->id, 'rol_id' => $adminRol->id,
            'nombre' => 'Admin', 'username' => 'adminauth', 'email' => 'adminauth@test.com',
            'password' => Hash::make('password'), 'estado' => 'activo',
        ]);

        $empleado = Empleado::create([
            'sucursal_id' => $this->sucursal->id, 'rol_id' => $mecRol->id,
            'nombre_completo' => 'Mec Test', 'ci' => '11111111', 'telefono' => '700001', 'estado' => true,
        ]);
        $esp = Especialidad::create(['nombre' => 'General', 'estado' => true]);
        $mecanico = Mecanico::create(['empleado_id' => $empleado->id, 'especialidad_id' => $esp->id, 'disponibilidad' => 'disponible']);

        $this->mecanicoUser = User::create([
            'empleado_id' => $empleado->id, 'sucursal_id' => $this->sucursal->id, 'rol_id' => $mecRol->id,
            'nombre' => 'Mecánico', 'username' => 'mecauto', 'email' => 'mecauto@test.com',
            'password' => Hash::make('password'), 'estado' => 'activo',
        ]);

        $this->cliente = Cliente::create([
            'nombre_completo' => 'Cliente Test', 'ci' => '22222222',
            'telefono' => '700002', 'fecha_registro' => now(), 'estado' => true,
        ]);
        $this->clienteUser = User::create([
            'cliente_id' => $this->cliente->id, 'rol_id' => $cliRol->id,
            'nombre' => 'Cliente', 'username' => 'cliauth', 'email' => 'cliauth@test.com',
            'password' => Hash::make('password'), 'estado' => 'activo',
        ]);

        $marca = MarcaVehiculo::create(['nombre' => 'Toyota', 'estado' => true]);
        $modelo = ModeloVehiculo::create(['marca_vehiculo_id' => $marca->id, 'nombre' => 'Corolla', 'estado' => true]);
        $vehiculo = Vehiculo::create([
            'cliente_id' => $this->cliente->id, 'modelo_vehiculo_id' => $modelo->id,
            'placa' => 'XYZ789', 'anio' => 2020, 'estado' => true,
        ]);

        $this->orden = OrdenTrabajo::create([
            'numero_orden' => 'OT-AUT-001', 'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $vehiculo->id, 'sucursal_id' => $this->sucursal->id,
            'usuario_recepcion_id' => $this->adminUser->id, 'fecha_emision' => now(),
            'descripcion_problema' => 'Revisión', 'estado' => 'en_proceso',
        ]);
    }

    public function test_mecanico_crea_solicitud(): void
    {
        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('admin.autorizaciones.store', ['orden_trabajo' => $this->orden->id]), [
                'titulo' => 'Reparación de frenos',
                'descripcion' => 'Pastillas de freno delanteras desgastadas. Requieren reemplazo.',
                'importe' => 250.00,
            ]);

        $response->assertRedirect(route('admin.autorizaciones.index'));

        $this->assertDatabaseHas('autorizaciones', [
            'orden_trabajo_id' => $this->orden->id,
            'titulo' => 'Reparación de frenos',
            'importe' => 250.00,
            'estado' => 'pendiente',
        ]);
    }

    public function test_cliente_ve_sus_autorizaciones(): void
    {
        Autorizacion::create([
            'orden_trabajo_id' => $this->orden->id,
            'usuario_solicitante_id' => $this->mecanicoUser->id,
            'titulo' => 'Frenos', 'descripcion' => 'Cambio pastillas',
            'importe' => 200, 'estado' => 'pendiente', 'fecha_solicitud' => now(),
        ]);

        $response = $this->actingAs($this->clienteUser)
            ->get(route('cliente.autorizaciones'));

        $response->assertOk();
        $response->assertSee('Frenos');
        $response->assertSee('$200.00');
    }

    public function test_cliente_autoriza_trabajo(): void
    {
        $auth = Autorizacion::create([
            'orden_trabajo_id' => $this->orden->id,
            'usuario_solicitante_id' => $this->mecanicoUser->id,
            'titulo' => 'Alineación', 'descripcion' => 'Requiere alineación',
            'importe' => 80, 'estado' => 'pendiente', 'fecha_solicitud' => now(),
        ]);

        $response = $this->actingAs($this->clienteUser)
            ->patch(route('cliente.autorizaciones.responder', $auth), [
                'accion' => 'autorizada',
                'comentario_cliente' => 'Proceder con el trabajo',
            ]);

        $response->assertRedirect(route('cliente.autorizaciones'));

        $auth->refresh();
        $this->assertEquals('autorizada', $auth->estado);
        $this->assertEquals('Proceder con el trabajo', $auth->comentario_cliente);
    }

    public function test_cliente_rechaza_trabajo(): void
    {
        $auth = Autorizacion::create([
            'orden_trabajo_id' => $this->orden->id,
            'usuario_solicitante_id' => $this->mecanicoUser->id,
            'titulo' => 'Pintura', 'descripcion' => 'Pintar puerta',
            'importe' => 500, 'estado' => 'pendiente', 'fecha_solicitud' => now(),
        ]);

        $response = $this->actingAs($this->clienteUser)
            ->patch(route('cliente.autorizaciones.responder', $auth), [
                'accion' => 'rechazada',
            ]);

        $response->assertRedirect(route('cliente.autorizaciones'));

        $this->assertEquals('rechazada', $auth->fresh()->estado);
    }

    public function test_cliente_pide_mas_informacion(): void
    {
        $auth = Autorizacion::create([
            'orden_trabajo_id' => $this->orden->id,
            'usuario_solicitante_id' => $this->mecanicoUser->id,
            'titulo' => 'Motor', 'descripcion' => 'Revisar motor',
            'importe' => 1200, 'estado' => 'pendiente', 'fecha_solicitud' => now(),
        ]);

        $response = $this->actingAs($this->clienteUser)
            ->patch(route('cliente.autorizaciones.responder', $auth), [
                'accion' => 'requiere_informacion',
                'comentario_cliente' => '¿Qué incluye exactamente?',
            ]);

        $response->assertRedirect(route('cliente.autorizaciones'));

        $this->assertEquals('requiere_informacion', $auth->fresh()->estado);
        $this->assertEquals('¿Qué incluye exactamente?', $auth->fresh()->comentario_cliente);
    }

    public function test_cliente_no_ve_autorizacion_de_otro(): void
    {
        $otroCliente = Cliente::create([
            'nombre_completo' => 'Otro', 'ci' => '33333333',
            'telefono' => '700003', 'fecha_registro' => now(), 'estado' => true,
        ]);
        $otroRol = Rol::firstOrCreate(['nombre' => 'Cliente'], ['estado' => true]);
        $otroUser = User::create([
            'cliente_id' => $otroCliente->id, 'rol_id' => $otroRol->id,
            'nombre' => 'Otro', 'username' => 'otrocliente', 'email' => 'otro@test.com',
            'password' => Hash::make('password'), 'estado' => 'activo',
        ]);
        $auth = Autorizacion::create([
            'orden_trabajo_id' => $this->orden->id,
            'usuario_solicitante_id' => $this->mecanicoUser->id,
            'titulo' => 'Ajeno', 'descripcion' => 'No deberías ver esto',
            'importe' => 999, 'estado' => 'pendiente', 'fecha_solicitud' => now(),
        ]);

        $response = $this->actingAs($otroUser)
            ->patch(route('cliente.autorizaciones.responder', $auth), [
                'accion' => 'autorizada',
            ]);

        $response->assertForbidden();
    }

    public function test_cliente_no_manipula_importe(): void
    {
        $auth = Autorizacion::create([
            'orden_trabajo_id' => $this->orden->id,
            'usuario_solicitante_id' => $this->mecanicoUser->id,
            'titulo' => 'Test', 'descripcion' => 'Desc',
            'importe' => 300, 'estado' => 'pendiente', 'fecha_solicitud' => now(),
        ]);

        $this->actingAs($this->clienteUser)
            ->patch(route('cliente.autorizaciones.responder', $auth), [
                'accion' => 'autorizada',
                'importe' => 1, // should be ignored
            ]);

        $this->assertEquals(300, $auth->fresh()->importe);
    }

    public function test_admin_cancela_solicitud(): void
    {
        $auth = Autorizacion::create([
            'orden_trabajo_id' => $this->orden->id,
            'usuario_solicitante_id' => $this->mecanicoUser->id,
            'titulo' => 'Test', 'descripcion' => 'Desc',
            'importe' => 150, 'estado' => 'pendiente', 'fecha_solicitud' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.autorizaciones.cancelar', $auth));

        $response->assertRedirect(route('admin.autorizaciones.index'));
        $this->assertEquals('cancelada', $auth->fresh()->estado);
    }
}
