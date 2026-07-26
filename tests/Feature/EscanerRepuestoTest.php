<?php

namespace Tests\Feature;

use App\Models\Repuesto;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EscanerRepuestoTest extends TestCase
{
    use DatabaseTransactions;

    protected User $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        $sucursal = Sucursal::inRandomOrder()->first() ?? Sucursal::factory()->create();
        $rolAdmin = Rol::where('nombre', 'Administrador')->first() ?? Rol::factory()->create(['nombre' => 'Administrador']);

        $this->usuario = User::factory()->create([
            'sucursal_id' => $sucursal->id,
            'rol_id' => $rolAdmin->id,
        ]);
    }

    public function test_codigo_existente_por_codigo_barras(): void
    {
        Repuesto::factory()->create([
            'codigo_barras' => 'TAL-ACE-000001',
            'codigo' => 'REP-001',
            'nombre' => 'Aceite de motor 5W-30',
            'marca' => 'Castrol',
            'categoria' => 'Lubricantes',
        ]);

        $response = $this->actingAs($this->usuario)
            ->getJson('/admin/repuestos/escaner/buscar?codigo=TAL-ACE-000001');

        $response->assertStatus(200)
            ->assertJson([
                'encontrado' => true,
                'codigo' => 'TAL-ACE-000001',
            ])
            ->assertJsonPath('repuesto.nombre', 'Aceite de motor 5W-30')
            ->assertJsonPath('repuesto.marca', 'Castrol');
    }

    public function test_codigo_existente_por_codigo_interno(): void
    {
        Repuesto::factory()->create([
            'codigo' => 'REP-999',
            'codigo_barras' => null,
            'nombre' => 'Filtro de aceite',
        ]);

        $response = $this->actingAs($this->usuario)
            ->getJson('/admin/repuestos/escaner/buscar?codigo=REP-999');

        $response->assertStatus(200)
            ->assertJson(['encontrado' => true])
            ->assertJsonPath('repuesto.nombre', 'Filtro de aceite');
    }

    public function test_codigo_existente_por_codigo_fabricante(): void
    {
        Repuesto::factory()->create([
            'codigo' => 'REP-777',
            'codigo_barras' => null,
            'codigo_fabricante' => 'OEM-BOSCH-123',
            'nombre' => 'Bujía Bosch',
        ]);

        $response = $this->actingAs($this->usuario)
            ->getJson('/admin/repuestos/escaner/buscar?codigo=OEM-BOSCH-123');

        $response->assertStatus(200)
            ->assertJson(['encontrado' => true])
            ->assertJsonPath('repuesto.nombre', 'Bujía Bosch');
    }

    public function test_codigo_desconocido_devuelve_encontrado_false(): void
    {
        $response = $this->actingAs($this->usuario)
            ->getJson('/admin/repuestos/escaner/buscar?codigo=NO-EXISTE');

        $response->assertStatus(200)
            ->assertJson([
                'encontrado' => false,
                'codigo' => 'NO-EXISTE',
            ]);
    }

    public function test_codigo_alfanumerico_con_guiones(): void
    {
        Repuesto::factory()->create([
            'codigo_barras' => 'TAL-ACE-000001',
            'nombre' => 'Aceite 5W-30',
        ]);

        $response = $this->actingAs($this->usuario)
            ->getJson('/admin/repuestos/escaner/buscar?codigo=TAL-ACE-000001');

        $response->assertStatus(200)->assertJson(['encontrado' => true]);
    }



    public function test_respuesta_es_siempre_json(): void
    {
        $response = $this->actingAs($this->usuario)
            ->getJson('/admin/repuestos/escaner/buscar?codigo=CUALQUIERA');

        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_escaneo_no_modifica_stock(): void
    {
        $repuesto = Repuesto::factory()->create([
            'codigo_barras' => 'TAL-ACE-000001',
            'nombre' => 'Aceite 5W-30',
        ]);

        $stockAntes = $repuesto->inventarios()->sum('cantidad_actual');

        $this->actingAs($this->usuario)
            ->getJson('/admin/repuestos/escaner/buscar?codigo=TAL-ACE-000001');

        $stockDespues = $repuesto->inventarios()->sum('cantidad_actual');

        $this->assertEquals($stockAntes, $stockDespues);
    }

    public function test_ruta_old_buscar_por_codigo_sigue_funcionando(): void
    {
        Repuesto::factory()->create([
            'codigo_barras' => 'BARCODE-OLD',
            'nombre' => 'Producto legacy',
        ]);

        $response = $this->actingAs($this->usuario)
            ->getJson('/admin/repuestos/buscar-por-codigo?codigo=BARCODE-OLD');

        $response->assertStatus(200)->assertJson(['encontrado' => true]);
    }
}
