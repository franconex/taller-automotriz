<?php

namespace Tests\Feature\Admin;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\Mecanico;
use App\Models\OrdenTrabajo;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\TipoServicio;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\MarcaVehiculo;
use App\Models\ModeloVehiculo;
use Carbon\Carbon;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\RolPermisoSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CitaCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected Sucursal $sucursal;
    protected Sucursal $otraSucursal;
    protected Rol $adminRol;
    protected Rol $recepcionRol;
    protected User $adminUser;
    protected User $recepcionUser;
    protected User $otroSucursalUser;
    protected Cliente $cliente;
    protected Vehiculo $vehiculo;
    protected Empleado $empleado;
    protected Mecanico $mecanico;
    protected Servicio $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolSeeder::class,
            PermisoSeeder::class,
            RolPermisoSeeder::class,
        ]);

        $this->sucursal = Sucursal::create([
            'nombre' => 'Sucursal Central',
            'direccion' => 'Av. Principal 123',
            'telefono' => '70000001',
            'estado' => true,
        ]);

        $this->otraSucursal = Sucursal::create([
            'nombre' => 'Sucursal Norte',
            'direccion' => 'Av. Norte 456',
            'telefono' => '70000002',
            'estado' => true,
        ]);

        $this->adminRol    = Rol::where('nombre', 'Administrador')->firstOrFail();
        $this->recepcionRol = Rol::where('nombre', 'Recepcionista')->firstOrFail();

        $this->adminUser = $this->makeUser('admin', $this->adminRol, $this->sucursal->id);
        $this->recepcionUser = $this->makeUser('recep', $this->recepcionRol, $this->sucursal->id);
        $this->otroSucursalUser = $this->makeUser('otro', $this->adminRol, $this->otraSucursal->id);

        $this->cliente = Cliente::create([
            'nombre_completo' => 'Juan Pérez',
            'ci' => '1234567',
            'telefono' => '70000010',
            'email' => 'juan@test.com',
            'direccion' => 'Calle 1',
            'fecha_registro' => now(),
            'estado' => true,
        ]);

        $marca = MarcaVehiculo::create(['nombre' => 'Toyota', 'estado' => true]);
        $modelo = ModeloVehiculo::create(['marca_vehiculo_id' => $marca->id, 'nombre' => 'Corolla', 'estado' => true]);

        $this->vehiculo = Vehiculo::create([
            'cliente_id' => $this->cliente->id,
            'modelo_vehiculo_id' => $modelo->id,
            'placa' => 'ABC123',
            'anio' => 2020,
            'estado' => true,
        ]);

        $this->empleado = Empleado::create([
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => $this->adminRol->id,
            'nombre_completo' => 'Carlos Mecánico',
            'ci' => '7654321',
            'telefono' => '70000020',
            'estado' => true,
        ]);

        $especialidad = Especialidad::create(['nombre' => 'Frenos', 'estado' => true]);

        $this->mecanico = Mecanico::create([
            'empleado_id' => $this->empleado->id,
            'especialidad_id' => $especialidad->id,
            'disponibilidad' => 'disponible',
        ]);

        $tipoServicio = TipoServicio::create([
            'nombre' => 'Mantenimiento general',
            'estado' => true,
        ]);

        $this->servicio = Servicio::create([
            'nombre' => 'Cambio de aceite',
            'tipo_servicio_id' => $tipoServicio->id,
            'precio_base' => 100,
            'estado' => true,
        ]);
    }

    protected function makeUser(string $username, Rol $rol, int $sucursalId): User
    {
        $empleado = Empleado::create([
            'sucursal_id' => $sucursalId,
            'rol_id' => $rol->id,
            'nombre_completo' => ucfirst($username),
            'ci' => '10000' . rand(100, 999),
            'telefono' => '7000000' . rand(0, 9),
            'estado' => true,
        ]);

        return User::create([
            'empleado_id' => $empleado->id,
            'sucursal_id' => $sucursalId,
            'rol_id' => $rol->id,
            'nombre' => ucfirst($username),
            'username' => $username,
            'email' => $username . '@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);
    }

    protected function datosValidos(array $overrides = []): array
    {
        $fecha = $overrides['fecha'] ?? Carbon::tomorrow()->toDateString();

        return array_merge([
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'sucursal_id' => $this->sucursal->id,
            'fecha' => $fecha,
            'hora' => '10:00',
            'hora_fin' => '11:00',
            'tipo' => 'mantenimiento',
            'descripcion_problema' => 'Cambio de aceite y revisión general',
            'costo_consulta' => 50,
            'deja_vehiculo' => 1,
            'observaciones' => 'Cliente nuevo',
            'estado' => 'pendiente',
            'servicio_id' => $this->servicio->id,
            'mecanico_id' => $this->mecanico->id,
        ], $overrides);
    }

    /* =============================================================
       Autenticación y autorización
       ============================================================= */

    public function test_visitante_no_accede_al_calendario(): void
    {
        $response = $this->get(route('admin.citas.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_administrador_puede_ver_calendario(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.citas.index'));

        $response->assertOk();
        $response->assertSee('Calendario de Citas');
    }

    public function test_recepcionista_con_permiso_puede_ver_y_crear(): void
    {
        $response = $this->actingAs($this->recepcionUser)
            ->get(route('admin.citas.index'));

        $response->assertOk();

        $payload = $this->datosValidos();
        $store = $this->actingAs($this->recepcionUser)
            ->postJson(route('admin.citas.store'), $payload);

        $store->assertStatus(201);
        $this->assertDatabaseHas('citas', [
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
        ]);
    }

    public function test_usuario_sin_permiso_recibe_403_en_acciones(): void
    {
        $sinPermisoRol = Rol::firstOrCreate(['nombre' => 'Mecánico'], ['descripcion' => 'Mec', 'estado' => true]);
        $sinPermiso = User::create([
            'empleado_id' => $this->empleado->id,
            'sucursal_id' => $this->sucursal->id,
            'rol_id' => $sinPermisoRol->id,
            'nombre' => 'Sin Permiso',
            'username' => 'sinperm',
            'email' => 'sin@test.com',
            'password' => Hash::make('password'),
            'estado' => 'activo',
        ]);

        $cita = Cita::create($this->datosValidos());

        $this->actingAs($sinPermiso)
            ->patchJson(route('admin.citas.confirmar', $cita))
            ->assertStatus(403);
    }

    /* =============================================================
       Endpoint de eventos
       ============================================================= */

    public function test_eventos_devuelve_json_valido(): void
    {
        $cita = Cita::create($this->datosValidos([
            'fecha' => Carbon::today()->addDays(2)->toDateString(),
        ]));

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.citas.eventos', [
                'start' => Carbon::today()->toDateString(),
                'end' => Carbon::today()->addDays(7)->toDateString(),
            ]));

        $response->assertOk();

        $data = $response->json();
        $this->assertIsArray($data);

        $encontrado = false;
        foreach ($data as $evento) {
            if (isset($evento['id']) && $evento['id'] === $cita->id) {
                $encontrado = true;
                $this->assertEquals('Juan Pérez', $evento['extendedProps']['cliente'] ?? null);
                $this->assertEquals('ABC123', $evento['extendedProps']['vehiculo'] ?? null);
                $this->assertEquals('pendiente', $evento['extendedProps']['estado'] ?? null);
                break;
            }
        }
        $this->assertTrue($encontrado, 'Cita no encontrada en eventos');
    }

    public function test_eventos_filtra_por_rango_de_fechas(): void
    {
        Cita::create($this->datosValidos(['fecha' => '2030-01-01']));

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.citas.eventos', [
                'start' => '2031-01-01',
                'end' => '2031-01-31',
            ]));

        $response->assertOk();
        $response->assertJsonCount(0);
    }

    public function test_eventos_respeta_filtro_por_estado(): void
    {
        $pendiente = Cita::create($this->datosValidos(['estado' => 'pendiente']));
        Cita::create($this->datosValidos([
            'estado' => 'confirmada',
            'fecha'  => Carbon::tomorrow()->addDays(1)->toDateString(),
        ]));

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.citas.eventos', [
                'start' => Carbon::today()->toDateString(),
                'end'   => Carbon::today()->addDays(7)->toDateString(),
                'estado' => 'pendiente',
            ]));

        $response->assertOk();
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals($pendiente->id, $data[0]['id']);
    }

    public function test_otra_sucursal_queda_protegida_en_eventos(): void
    {
        $response = $this->actingAs($this->otroSucursalUser)
            ->getJson(route('admin.citas.eventos', [
                'start' => Carbon::today()->toDateString(),
                'end'   => Carbon::today()->addDays(7)->toDateString(),
            ]));

        $response->assertOk();
    }

    /* =============================================================
       Validaciones de creación
       ============================================================= */

    public function test_cliente_y_vehiculo_deben_coincidir(): void
    {
        $otroCliente = Cliente::create([
            'nombre_completo' => 'María López',
            'ci' => '9999999',
            'telefono' => '70000099',
            'fecha_registro' => now(),
            'estado' => true,
        ]);

        $this->withoutExceptionHandling();
        try {
            $this->actingAs($this->adminUser)
                ->json('POST', route('admin.citas.store'), $this->datosValidos(['cliente_id' => $otroCliente->id]));
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('vehiculo_id', $e->errors());
            return;
        }
        $this->fail('Expected ValidationException was not thrown.');
    }

    public function test_no_se_permiten_cruces_del_mismo_vehiculo(): void
    {
        $fecha = Carbon::tomorrow()->toDateString();

        $primera = Cita::create($this->datosValidos([
            'fecha' => $fecha,
            'hora' => '10:00',
            'hora_fin' => '11:00',
        ]));

        $this->withoutExceptionHandling();
        try {
            $this->actingAs($this->adminUser)
                ->json('POST', route('admin.citas.store'), $this->datosValidos([
                    'fecha' => $fecha,
                    'hora' => '10:30',
                    'hora_fin' => '11:30',
                ]));
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('vehiculo_id', $e->errors());
            return;
        }
        $this->fail('Expected ValidationException was not thrown.');
    }

    public function test_no_se_permiten_cruces_del_mismo_mecanico(): void
    {
        $fecha = Carbon::tomorrow()->toDateString();

        Cita::create($this->datosValidos([
            'fecha' => $fecha,
            'hora' => '10:00',
            'hora_fin' => '11:00',
        ]));

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.citas.store'), $this->datosValidos([
                'fecha' => Carbon::tomorrow()->addDays(1)->toDateString(),
                'hora' => '10:30',
                'hora_fin' => '11:30',
            ]));

        $response->assertStatus(201);

        $this->withoutExceptionHandling();
        try {
            $this->actingAs($this->adminUser)
                ->json('POST', route('admin.citas.store'), $this->datosValidos([
                    'fecha' => Carbon::tomorrow()->addDays(1)->toDateString(),
                    'hora' => '10:15',
                    'hora_fin' => '11:00',
                ]));
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('mecanico_id', $e->errors());
            return;
        }
        $this->fail('Expected ValidationException was not thrown.');
    }

    public function test_puede_crear_cita_valida(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.citas.store'), $this->datosValidos());

        $response->assertStatus(201);
        $this->assertDatabaseHas('citas', [
            'cliente_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehiculo->id,
            'mecanico_id' => $this->mecanico->id,
            'estado' => 'pendiente',
        ]);
    }

    public function test_formulario_rechaza_datos_invalidos(): void
    {
        $this->withoutExceptionHandling();
        try {
            $this->actingAs($this->adminUser)
                ->json('POST', route('admin.citas.store'), [
                    'cliente_id' => 999999,
                    'vehiculo_id' => 999999,
                    'sucursal_id' => 999999,
                    'fecha' => 'no-fecha',
                    'hora' => '25:00',
                    'descripcion_problema' => '',
                ]);
        } catch (ValidationException $e) {
            $campos = array_keys($e->errors());
            $this->assertNotEmpty(array_intersect(['cliente_id', 'vehiculo_id', 'sucursal_id', 'fecha', 'hora', 'descripcion_problema'], $campos));
            return;
        }
        $this->fail('Expected ValidationException was not thrown.');
    }

    /* =============================================================
       Acciones: confirmar / reprogramar / cancelar / no-asistio
       ============================================================= */

    public function test_se_puede_confirmar_una_cita_pendiente(): void
    {
        $cita = Cita::create($this->datosValidos());

        $response = $this->actingAs($this->adminUser)
            ->patchJson(route('admin.citas.confirmar', $cita));

        $response->assertOk();
        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    public function test_no_se_puede_confirmar_una_cita_cancelada(): void
    {
        $cita = Cita::create($this->datosValidos(['estado' => 'cancelada']));

        $response = $this->actingAs($this->adminUser)
            ->patchJson(route('admin.citas.confirmar', $cita));

        $response->assertStatus(422);
    }

    public function test_se_puede_reprogramar_una_cita(): void
    {
        $cita = Cita::create($this->datosValidos());

        $nuevaFecha = Carbon::tomorrow()->addDays(5)->toDateString();

        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.citas.reprogramar', $cita), [
                'fecha' => $nuevaFecha,
                'hora'  => '14:00',
                'hora_fin' => '15:00',
                'sucursal_id' => $this->sucursal->id,
                'motivo_reprogramacion' => 'Cliente solicitó cambio de día',
            ]);

        $response->assertOk();
        $this->assertEquals($nuevaFecha, $cita->fresh()->fecha->toDateString());
        $this->assertEquals('14:00', $cita->fresh()->hora);
        $this->assertEquals('Cliente solicitó cambio de día', $cita->fresh()->motivo_reprogramacion);
    }

    public function test_cancelar_no_elimina_el_registro(): void
    {
        $cita = Cita::create($this->datosValidos());

        $response = $this->actingAs($this->adminUser)
            ->patchJson(route('admin.citas.cancelar', $cita), [
                'cancelado_motivo' => 'Cliente no podrá asistir',
            ]);

        $response->assertOk();
        $this->assertNotNull($cita->fresh());
        $this->assertEquals('cancelada', $cita->fresh()->estado);
        $this->assertEquals('Cliente no podrá asistir', $cita->fresh()->cancelado_motivo);
        $this->assertEquals($this->adminUser->id, $cita->fresh()->cancelado_por_id);
    }

    public function test_cancelar_sin_motivo_falla(): void
    {
        $cita = Cita::create($this->datosValidos());

        $this->withoutExceptionHandling();
        try {
            $this->actingAs($this->adminUser)
                ->json('PATCH', route('admin.citas.cancelar', $cita), []);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('cancelado_motivo', $e->errors());
            return;
        }
        $this->fail('Expected ValidationException was not thrown.');
    }

    public function test_no_asistio_requiere_fecha_pasada(): void
    {
        $futura = Cita::create($this->datosValidos([
            'fecha' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'hora' => '15:00',
        ]));

        $response = $this->actingAs($this->adminUser)
            ->patchJson(route('admin.citas.no-asistio', $futura));

        $response->assertStatus(422);

        $pasada = Cita::create($this->datosValidos([
            'fecha' => Carbon::yesterday()->toDateString(),
            'hora' => '10:00',
            'hora_fin' => '11:00',
            'estado' => 'confirmada',
        ]));

        $response = $this->actingAs($this->adminUser)
            ->patchJson(route('admin.citas.no-asistio', $pasada));

        $response->assertOk();
        $this->assertEquals('no_asistio', $pasada->fresh()->estado);
    }

    /* =============================================================
       Endpoint show (JSON)
       ============================================================= */

    public function test_show_devuelve_json_completo(): void
    {
        $cita = Cita::create($this->datosValidos());

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.citas.show', $cita));

        $response->assertOk()
            ->assertJsonStructure([
                'id', 'cliente', 'vehiculo', 'servicio', 'mecanico',
                'fecha', 'hora', 'estado', 'estado_label', 'estado_color',
                'es_pasable_reprogramar', 'es_pasable_confirmar',
            ])
            ->assertJson(['id' => $cita->id]);
    }

    /* =============================================================
       Tabla del día + próximas
       ============================================================= */

    public function test_tabla_del_dia_devuelve_citas(): void
    {
        Cita::create($this->datosValidos([
            'fecha' => Carbon::tomorrow()->toDateString(),
        ]));

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.citas.tabla-dia', [
                'fecha' => Carbon::tomorrow()->toDateString(),
            ]));

        $response->assertOk()
            ->assertJsonStructure([
                'fecha',
                'citas' => [['id', 'hora', 'cliente', 'vehiculo', 'servicio', 'mecanico', 'estado']],
                'puede_editar',
                'puede_cancelar',
            ]);

        $this->assertCount(1, $response->json('citas'));
    }

    public function test_proximas_devuelve_citas_futuras(): void
    {
        Cita::create($this->datosValidos([
            'fecha' => Carbon::tomorrow()->addDays(1)->toDateString(),
        ]));

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('admin.citas.proximas'));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, count($response->json('citas')));
    }

    /* =============================================================
       Convertir a orden
       ============================================================= */

    public function test_convertir_a_orden_crea_orden_y_marca_atendida(): void
    {
        $cita = Cita::create($this->datosValidos());

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.citas.convertir-orden', $cita));

        $response->assertOk();
        $this->assertNotNull($cita->fresh()->ordenTrabajo);
        $this->assertEquals('atendida', $cita->fresh()->estado);
    }

    public function test_no_se_puede_convertir_a_orden_si_ya_tiene_una(): void
    {
        $cita = Cita::create($this->datosValidos());
        OrdenTrabajo::create([
            'numero_orden' => 'OT-000099',
            'cliente_id' => $cita->cliente_id,
            'vehiculo_id' => $cita->vehiculo_id,
            'sucursal_id' => $cita->sucursal_id,
            'usuario_recepcion_id' => $this->adminUser->id,
            'cita_id' => $cita->id,
            'fecha_emision' => now(),
            'descripcion_problema' => $cita->descripcion_problema,
            'estado' => 'recibida',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.citas.convertir-orden', $cita));

        $response->assertStatus(422);
    }

    /* =============================================================
       Edición
       ============================================================= */

    public function test_puede_editar_cita(): void
    {
        $cita = Cita::create($this->datosValidos());

        $response = $this->actingAs($this->adminUser)
            ->putJson(route('admin.citas.update', $cita), $this->datosValidos([
                'observaciones' => 'Editado',
            ]));

        $response->assertOk();
        $this->assertEquals('Editado', $cita->fresh()->observaciones);
    }
}
