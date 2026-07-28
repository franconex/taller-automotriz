<?php

namespace Tests\Feature\Admin;

use App\Models\AsignacionTrabajo;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\Mecanico;
use App\Models\OrdenTrabajo;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\MarcaVehiculo;
use App\Models\ModeloVehiculo;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\RolPermisoSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MecanicoPanelTest extends TestCase
{
    use RefreshDatabase;

    private Sucursal $sucursal;
    private Rol $mecanicoRol;
    private Rol $adminRol;
    private User $mecanicoUser;
    private User $adminUser;
    private Mecanico $mecanico;
    private Cliente $cliente;
    private Vehiculo $vehiculo;
    private OrdenTrabajo $ordenAsignada;
    private OrdenTrabajo $ordenNoAsignada;
    private AsignacionTrabajo $asignacion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RolSeeder::class, PermisoSeeder::class, RolPermisoSeeder::class]);

        $this->sucursal = Sucursal::create([
            'nombre' => 'Sucursal Test', 'direccion' => 'Dir Test',
            'telefono' => '12345678', 'horario_atencion' => 'Lun-Vie 8-18', 'estado' => true,
        ]);

        $this->mecanicoRol = Rol::where('nombre', 'Mecánico')->firstOrFail();
        $this->adminRol = Rol::where('nombre', 'Administrador')->firstOrFail();

        $empleado = Empleado::create([
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => $this->mecanicoRol->id,
            'nombre_completo' => 'Mecánico Test',
            'ci' => '11111111', 'telefono' => '70000001', 'estado' => true,
        ]);

        $this->mecanicoUser = User::create([
            'empleado_id' => $empleado->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => $this->mecanicoRol->id,
            'nombre' => 'Mecánico Test',
            'username' => 'mecanico',
            'email' => 'mecanico@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $this->adminUser = User::create([
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => $this->adminRol->id,
            'nombre' => 'Admin',
            'username' => 'admin2',
            'email' => 'admin2@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $especialidad = Especialidad::create(['nombre' => 'Frenos', 'estado' => true]);

        $this->mecanico = Mecanico::create([
            'empleado_id' => $empleado->id,
            'especialidad_id' => $especialidad->id,
            'disponibilidad' => 'disponible',
        ]);

        $this->cliente = Cliente::create([
            'nombre_completo' => 'Juan Pérez', 'ci' => '1234567',
            'telefono' => '70000010', 'fecha_registro' => now(), 'estado' => true,
        ]);

        $marca = MarcaVehiculo::create(['nombre' => 'Toyota', 'estado' => true]);
        $modelo = ModeloVehiculo::create(['marca_vehiculo_id' => $marca->id, 'nombre' => 'Corolla', 'estado' => true]);

        $this->vehiculo = Vehiculo::create([
            'cliente_id' => $this->cliente->id,
            'modelo_vehiculo_id' => $modelo->id,
            'placa' => 'ABC123', 'anio' => 2020, 'estado' => true,
        ]);

        $this->ordenAsignada = OrdenTrabajo::create([
            'numero_orden' => 'OT-001',
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'sucursal_id' => $this->sucursal->id,
            'usuario_recepcion_id' => $this->adminUser->id,
            'fecha_emision' => now(),
            'descripcion_problema' => 'Revisión general',
            'estado' => 'recibida',
        ]);

        $this->ordenNoAsignada = OrdenTrabajo::create([
            'numero_orden' => 'OT-002',
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'sucursal_id' => $this->sucursal->id,
            'usuario_recepcion_id' => $this->adminUser->id,
            'fecha_emision' => now(),
            'descripcion_problema' => 'Otro trabajo',
            'estado' => 'recibida',
        ]);

        $this->asignacion = AsignacionTrabajo::create([
            'orden_trabajo_id' => $this->ordenAsignada->id,
            'mecanico_id' => $this->mecanico->id,
            'usuario_asignador_id' => $this->adminUser->id,
            'actividad_asignada' => 'Revisión completa',
            'prioridad' => 'normal',
            'estado' => 'activa',
            'fecha_asignacion' => now(),
        ]);
    }

    /* =============================================================
       ACCESO AL PANEL
       ============================================================= */

    public function test_mecanico_ve_sus_ordenes(): void
    {
        $response = $this->actingAs($this->mecanicoUser)->get(route('admin.ordenes.index'));

        $response->assertOk();
        $response->assertSee('OT-001');
    }

    public function test_admin_ve_todas_las_ordenes(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.ordenes.index'));

        $response->assertOk();
        $response->assertSee('Órdenes de trabajo');
    }

    /* =============================================================
       VER ORDEN ASIGNADA
       ============================================================= */

    public function test_mecanico_ve_orden_asignada(): void
    {
        $response = $this->actingAs($this->mecanicoUser)
            ->get(route('admin.ordenes.show', $this->ordenAsignada));

        $response->assertOk();
        $response->assertSee('OT-001');
        $response->assertSee('Juan Pérez');
        $response->assertSee('ABC123');
    }

    public function test_mecanico_recibe_403_si_orden_no_asignada(): void
    {
        $response = $this->actingAs($this->mecanicoUser)
            ->get(route('admin.ordenes.show', $this->ordenNoAsignada));

        $response->assertForbidden();
    }

    /* =============================================================
       INICIAR TRABAJO
       ============================================================= */

    public function test_mecanico_inicia_trabajo(): void
    {
        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('mecanico.ordenes.diagnostico', $this->ordenAsignada));

        $response->assertRedirect(route('admin.ordenes.show', $this->ordenAsignada));

        $this->ordenAsignada->refresh();
        $this->assertEquals('en_diagnostico', $this->ordenAsignada->estado);

        $this->asignacion->refresh();
        $this->assertNotNull($this->asignacion->fecha_inicio);
    }

    public function test_mecanico_no_inicia_trabajo_si_no_asignado(): void
    {
        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('mecanico.ordenes.diagnostico', $this->ordenNoAsignada));

        $response->assertForbidden();
    }

    /* =============================================================
       REGISTRAR DIAGNÓSTICO
       ============================================================= */

    public function test_mecanico_registra_diagnostico(): void
    {
        $this->ordenAsignada->update(['estado' => 'diagnostico']);

        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('mecanico.ordenes.diagnostico', $this->ordenAsignada), [
                'diagnostico_mecanico' => 'Freno delantero izquierdo desgastado',
            ]);

        $response->assertRedirect(route('admin.ordenes.show', $this->ordenAsignada));

        $this->ordenAsignada->refresh();
        $this->assertEquals('en_proceso', $this->ordenAsignada->estado);
        $this->assertEquals('Freno delantero izquierdo desgastado', $this->ordenAsignada->diagnostico_general);

        $this->asignacion->refresh();
        $this->assertEquals('Freno delantero izquierdo desgastado', $this->asignacion->diagnostico_mecanico);
    }

    /* =============================================================
       REGISTRAR AVANCE
       ============================================================= */

    public function test_mecanico_registra_avance(): void
    {
        $this->ordenAsignada->update(['estado' => 'en_proceso']);

        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('mecanico.ordenes.avances', $this->ordenAsignada), [
                'porcentaje_avance' => 50,
                'proximo_paso' => 'Revisar freno trasero',
            ]);

        $response->assertRedirect(route('admin.ordenes.show', $this->ordenAsignada));

        $this->asignacion->refresh();
        $this->assertEquals(50, $this->asignacion->porcentaje_avance);
        $this->assertEquals('Revisar freno trasero', $this->asignacion->proximo_paso);
    }

    /* =============================================================
       PAUSAR Y REANUDAR
       ============================================================= */

    public function test_mecanico_pausa_trabajo(): void
    {
        $this->ordenAsignada->update(['estado' => 'en_proceso']);

        $response = $this->actingAs($this->mecanicoUser)
            ->patch(route('mecanico.ordenes.estado', $this->ordenAsignada), [
                'estado' => 'pausada',
            ]);

        $response->assertRedirect(route('admin.ordenes.show', $this->ordenAsignada));
        $this->ordenAsignada->refresh();
        $this->assertEquals('pausada', $this->ordenAsignada->estado);
    }

    public function test_mecanico_reanuda_trabajo_con_diagnostico_a_en_proceso(): void
    {
        $this->asignacion->update(['diagnostico_mecanico' => 'Diagnóstico previo realizado']);
        $this->ordenAsignada->update(['estado' => 'pausada', 'diagnostico_general' => 'Diagnóstico previo realizado']);

        $response = $this->actingAs($this->mecanicoUser)
            ->patch(route('mecanico.ordenes.estado', $this->ordenAsignada), [
                'estado' => 'en_proceso',
            ]);

        $response->assertRedirect(route('admin.ordenes.show', $this->ordenAsignada));
        $this->ordenAsignada->refresh();
        $this->assertEquals('en_proceso', $this->ordenAsignada->estado);
    }

    public function test_mecanico_reanuda_trabajo_sin_diagnostico_a_diagnostico(): void
    {
        $this->ordenAsignada->update(['estado' => 'pausada']);

        $response = $this->actingAs($this->mecanicoUser)
            ->patch(route('mecanico.ordenes.estado', $this->ordenAsignada), [
                'estado' => 'en_diagnostico',
            ]);

        $response->assertRedirect(route('admin.ordenes.show', $this->ordenAsignada));
        $this->ordenAsignada->refresh();
        $this->assertEquals('en_diagnostico', $this->ordenAsignada->estado);
    }

    /* =============================================================
       FINALIZAR
       ============================================================= */

    public function test_mecanico_finaliza_trabajo(): void
    {
        $this->ordenAsignada->update(['estado' => 'en_proceso']);

        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('mecanico.ordenes.finalizar', $this->ordenAsignada));

        $response->assertRedirect(route('admin.ordenes.index'));
        $this->ordenAsignada->refresh();
        $this->assertEquals('finalizada', $this->ordenAsignada->estado);

        $this->asignacion->refresh();
        $this->assertNotNull($this->asignacion->fecha_finalizacion);
        $this->assertEquals(100, $this->asignacion->porcentaje_avance);
    }

    /* =============================================================
       TRANSICIONES NO VÁLIDAS
       ============================================================= */

    public function test_no_pasa_de_recibida_a_finalizada(): void
    {
        $this->ordenAsignada->update(['estado' => 'recibida']);

        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('mecanico.ordenes.finalizar', $this->ordenAsignada));

        $response->assertStatus(422);
    }

    /* =============================================================
       NOTAS INTERNAS
       ============================================================= */

    public function test_mecanico_crea_nota_interna(): void
    {
        $this->ordenAsignada->update(['estado' => 'en_proceso']);

        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('mecanico.ordenes.diagnostico', $this->ordenAsignada), [
                'contenido' => 'Necesito herramienta X',
                'visible_cliente' => false,
            ]);

        $response->assertRedirect(route('admin.ordenes.show', $this->ordenAsignada));

        $this->assertDatabaseHas('notas_trabajo', [
            'asignacion_trabajo_id' => $this->asignacion->id,
            'contenido' => 'Necesito herramienta X',
            'visible_cliente' => false,
        ]);
    }

    public function test_mecanico_crea_nota_visible_cliente(): void
    {
        $this->ordenAsignada->update(['estado' => 'en_proceso']);

        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('mecanico.ordenes.diagnostico', $this->ordenAsignada), [
                'contenido' => 'El trabajo va bien',
                'visible_cliente' => true,
            ]);

        $response->assertRedirect(route('admin.ordenes.show', $this->ordenAsignada));

        $this->assertDatabaseHas('notas_trabajo', [
            'asignacion_trabajo_id' => $this->asignacion->id,
            'contenido' => 'El trabajo va bien',
            'visible_cliente' => true,
        ]);
    }

    /* =============================================================
       EVIDENCIAS
       ============================================================= */

    public function test_mecanico_sube_evidencia(): void
    {
        $this->ordenAsignada->update(['estado' => 'en_proceso']);
        Storage::fake('public');

        $archivo = UploadedFile::fake()->image('freno.jpg', 200, 200);

        $response = $this->actingAs($this->mecanicoUser)
            ->post(route('mecanico.ordenes.evidencias', $this->ordenAsignada), [
                'archivo' => $archivo,
                'descripcion' => 'Freno delantero desgastado',
            ]);

        $response->assertRedirect(route('admin.ordenes.show', $this->ordenAsignada));

        $this->assertDatabaseHas('evidencias_trabajo', [
            'asignacion_trabajo_id' => $this->asignacion->id,
            'descripcion' => 'Freno delantero desgastado',
        ]);

        Storage::disk('public')->assertExists('evidencias/' . $archivo->hashName());
    }

    /* =============================================================
       ADMIN SUPERVISOR
       ============================================================= */

    public function test_admin_ve_orden_como_supervisor(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.ordenes.show', $this->ordenAsignada));

        $response->assertOk();
        $response->assertSee('OT-001');
    }

    /* =============================================================
       MECÁNICO SIN ASIGNACIÓN
       ============================================================= */

    public function test_mecanico_sin_asignaciones_ve_panel_vacio(): void
    {
        $empleado2 = Empleado::create([
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => $this->mecanicoRol->id,
            'nombre_completo' => 'Mecánico Sin Órdenes',
            'ci' => '22222222', 'telefono' => '70000002', 'estado' => true,
        ]);

        $user2 = User::create([
            'empleado_id' => $empleado2->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => $this->mecanicoRol->id,
            'nombre' => 'Mecánico Sin Órdenes',
            'username' => 'mecanico2',
            'email' => 'mecanico2@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        Mecanico::create([
            'empleado_id' => $empleado2->id,
            'especialidad_id' => Especialidad::create(['nombre' => 'Motor', 'estado' => true])->id,
            'disponibilidad' => 'disponible',
        ]);

        $response = $this->actingAs($user2)->get(route('admin.ordenes.index'));

        $response->assertOk();
        $response->assertDontSee('OT-001');
    }
}
