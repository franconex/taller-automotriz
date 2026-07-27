<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\DetalleOrdenTrabajo;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\Inventario;
use App\Models\Mecanico;
use App\Models\MetodoPago;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\TipoServicio;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $sucursal = Sucursal::first() ?? Sucursal::create([
            'nombre' => 'Sucursal Principal',
            'direccion' => 'Av. Principal #100, Santa Cruz',
            'telefono' => '3-1234567',
            'horario_atencion' => 'Lun-Vie 8:00-18:00, Sab 8:00-13:00',
        ]);

        $adminRole = Rol::where('nombre', 'Administrador')->first();
        $gerenteRole = Rol::where('nombre', 'Gerente')->first();
        $recepcionistaRole = Rol::where('nombre', 'Recepcionista')->first();
        $mecanicoRole = Rol::where('nombre', 'Mecanico')->first();

        $this->command->info('Creando empleados...');

        $empleadoAdmin = Empleado::firstOrCreate(
            ['ci' => '12345678'],
            [
                'sucursal_id' => $sucursal->id,
                'rol_id' => $adminRole->id,
                'nombre_completo' => 'Admin Principal',
                'telefono' => '70000001',
                'email' => 'admin@tallerpro.com',
                'cargo' => 'Administrador del Sistema',
                'fecha_contratacion' => '2024-01-15',
                'estado' => true,
            ]
        );

        $empleadoGerente = Empleado::firstOrCreate(
            ['ci' => '87654321'],
            [
                'sucursal_id' => $sucursal->id,
                'rol_id' => $gerenteRole->id,
                'nombre_completo' => 'Maria Garcia',
                'telefono' => '70000002',
                'email' => 'maria@tallerpro.com',
                'cargo' => 'Gerente General',
                'fecha_contratacion' => '2024-02-01',
                'estado' => true,
            ]
        );

        $empleadoRecepcion = Empleado::firstOrCreate(
            ['ci' => '11223344'],
            [
                'sucursal_id' => $sucursal->id,
                'rol_id' => $recepcionistaRole->id,
                'nombre_completo' => 'Carlos Mendoza',
                'telefono' => '70000003',
                'email' => 'carlos@tallerpro.com',
                'cargo' => 'Recepcionista',
                'fecha_contratacion' => '2024-03-10',
                'estado' => true,
            ]
        );

        $empleadoMec1 = Empleado::firstOrCreate(
            ['ci' => '99887766'],
            [
                'sucursal_id' => $sucursal->id,
                'rol_id' => $mecanicoRole->id,
                'nombre_completo' => 'Juan Perez',
                'telefono' => '70000004',
                'email' => 'juan@tallerpro.com',
                'cargo' => 'Mecanico Senior',
                'fecha_contratacion' => '2024-01-20',
                'estado' => true,
            ]
        );

        $empleadoMec2 = Empleado::firstOrCreate(
            ['ci' => '55667788'],
            [
                'sucursal_id' => $sucursal->id,
                'rol_id' => $mecanicoRole->id,
                'nombre_completo' => 'Pedro Ramirez',
                'telefono' => '70000005',
                'email' => 'pedro@tallerpro.com',
                'cargo' => 'Mecanico',
                'fecha_contratacion' => '2024-04-05',
                'estado' => true,
            ]
        );

        $empleadoMec3 = Empleado::firstOrCreate(
            ['ci' => '33445566'],
            [
                'sucursal_id' => $sucursal->id,
                'rol_id' => $mecanicoRole->id,
                'nombre_completo' => 'Luis Fernandez',
                'telefono' => '70000006',
                'email' => 'luis@tallerpro.com',
                'cargo' => 'Mecanico Electricista',
                'fecha_contratacion' => '2024-05-12',
                'estado' => true,
            ]
        );

        $this->command->info('Asociando empleados a usuarios...');
        $users = User::all();
        $userAdmin = $users->firstWhere('username', 'admin');
        $userGerente = $users->firstWhere('username', 'gerente');
        $userRecepcion = $users->firstWhere('username', 'recepcion');

        if ($userAdmin) { $userAdmin->update(['empleado_id' => $empleadoAdmin->id]); }
        if ($userGerente) { $userGerente->update(['empleado_id' => $empleadoGerente->id]); }
        if ($userRecepcion) { $userRecepcion->update(['empleado_id' => $empleadoRecepcion->id]); }

        $this->command->info('Creando especialidades...');
        $espMeca = Especialidad::firstOrCreate(['nombre' => 'Mecanica General'], ['descripcion' => 'Mecanica general vehicular', 'estado' => true]);
        $espElec = Especialidad::firstOrCreate(['nombre' => 'Electricidad Automotriz'], ['descripcion' => 'Electricidad y electronica automotriz', 'estado' => true]);
        $espMotor = Especialidad::firstOrCreate(['nombre' => 'Motores Diesel'], ['descripcion' => 'Especializacion en motores diesel', 'estado' => true]);

        $this->command->info('Creando mecanicos...');
        $mecanico1 = Mecanico::firstOrCreate(
            ['empleado_id' => $empleadoMec1->id],
            ['especialidad_id' => $espMeca->id, 'disponibilidad' => 'disponible', 'observaciones' => 'Mecanico general con 8 anos de experiencia']
        );
        $mecanico2 = Mecanico::firstOrCreate(
            ['empleado_id' => $empleadoMec2->id],
            ['especialidad_id' => $espMotor->id, 'disponibilidad' => 'ocupado', 'observaciones' => 'Especialista en motores diesel']
        );
        $mecanico3 = Mecanico::firstOrCreate(
            ['empleado_id' => $empleadoMec3->id],
            ['especialidad_id' => $espElec->id, 'disponibilidad' => 'disponible', 'observaciones' => 'Electricista automotriz']
        );

        $adminPassword = 'TallerPro2026!';

        $userMec1 = User::firstOrCreate(
            ['username' => 'mecanico1'],
            [
                'nombre' => $empleadoMec1->nombre_completo,
                'email' => 'mecanico1@tallerpro.com',
                'password' => Hash::make($adminPassword),
                'estado' => 'activo',
                'rol_id' => $mecanicoRole->id,
                'sucursal_id' => $sucursal->id,
                'empleado_id' => $empleadoMec1->id,
            ]
        );

        $userMec2 = User::firstOrCreate(
            ['username' => 'mecanico2'],
            [
                'nombre' => $empleadoMec2->nombre_completo,
                'email' => 'mecanico2@tallerpro.com',
                'password' => Hash::make($adminPassword),
                'estado' => 'activo',
                'rol_id' => $mecanicoRole->id,
                'sucursal_id' => $sucursal->id,
                'empleado_id' => $empleadoMec2->id,
            ]
        );

        $this->command->info('Creando proveedores...');
        $prov1 = Proveedor::firstOrCreate(
            ['nit' => '102345027'],
            [
                'nombre_empresa' => 'Importadora de Repuestos Santa Cruz',
                'contacto' => 'Roberto Vaca',
                'telefono' => '3-3456789',
                'email' => 'ventas@importadorasc.com',
                'direccion' => 'Av. Cristo Redentor #500',
                'estado' => true,
            ]
        );
        $prov2 = Proveedor::firstOrCreate(
            ['nit' => '201987654'],
            [
                'nombre_empresa' => 'Lubricantes y Filtros Bolivia',
                'contacto' => 'Ana Laura',
                'telefono' => '3-9876543',
                'email' => 'info@lubribol.com',
                'direccion' => 'Calle Buenos Aires #300',
                'estado' => true,
            ]
        );
        $prov3 = Proveedor::firstOrCreate(
            ['nit' => '305678901'],
            [
                'nombre_empresa' => 'Distribuidora de Frenos ABC',
                'contacto' => 'Mario Suarez',
                'telefono' => '3-4567890',
                'email' => 'pedidos@frenosabc.com',
                'direccion' => 'Av. Grigota #750',
                'estado' => true,
            ]
        );

        $this->command->info('Creando tipos de servicio...');
        $tipoMeca = TipoServicio::firstOrCreate(['nombre' => 'Mecanica General'], ['descripcion' => 'Servicios de mecanica general', 'estado' => true]);
        $tipoElec = TipoServicio::firstOrCreate(['nombre' => 'Electricidad'], ['descripcion' => 'Servicios de electricidad automotriz', 'estado' => true]);
        $tipoRev = TipoServicio::firstOrCreate(['nombre' => 'Revision y Diagnostico'], ['descripcion' => 'Revision y diagnostico vehicular', 'estado' => true]);
        $tipoLat = TipoServicio::firstOrCreate(['nombre' => 'Latoneria y Pintura'], ['descripcion' => 'Trabajos de latoneria y pintura', 'estado' => true]);
        $tipoMan = TipoServicio::firstOrCreate(['nombre' => 'Mantenimiento Preventivo'], ['descripcion' => 'Mantenimiento preventivo programado', 'estado' => true]);

        $this->command->info('Creando servicios...');
        $servicios = [];
        $serviciosData = [
            ['Cambio de Aceite y Filtro', $tipoMan->id, 80, 30],
            ['Alineacion y Balanceo', $tipoMeca->id, 120, 45],
            ['Revision de Frenos', $tipoMeca->id, 60, 20],
            ['Cambio de Pastillas de Freno', $tipoMeca->id, 150, 60],
            ['Diagnostico Computarizado', $tipoRev->id, 200, 45],
            ['Revision General', $tipoRev->id, 100, 30],
            ['Cambio de Bujias', $tipoMeca->id, 90, 40],
            ['Reparacion de Motor Electrico', $tipoElec->id, 350, 120],
            ['Instalacion de Alarma', $tipoElec->id, 180, 60],
            ['Cambio de Alternador', $tipoElec->id, 120, 50],
            ['Reparacion de Aire Acondicionado', $tipoElec->id, 400, 90],
            ['Pintura Completa', $tipoLat->id, 2500, 240],
            ['Reparacion de Parachoques', $tipoLat->id, 400, 120],
            ['Latoneria Menor', $tipoLat->id, 300, 90],
            ['Cambio de Amortiguadores', $tipoMeca->id, 200, 60],
            ['Lavado de Inyectores', $tipoMeca->id, 150, 45],
            ['Cambio de Correa de Distribucion', $tipoMeca->id, 500, 180],
            ['Escaneo Electronico', $tipoRev->id, 150, 30],
            ['Revision de Suspension', $tipoMeca->id, 80, 30],
            ['Cambio de Embrague', $tipoMeca->id, 800, 240],
        ];
        foreach ($serviciosData as $i => $s) {
            $servicios[] = Servicio::firstOrCreate(
                ['nombre' => $s[0], 'tipo_servicio_id' => $s[1]],
                [
                    'descripcion' => "Servicio de {$s[0]}",
                    'precio_base' => $s[2],
                    'duracion_estimada_minutos' => $s[3],
                    'estado' => true,
                ]
            );
        }

        $this->command->info('Creando repuestos...');
        $repuestosData = [
            ['REP-001', 'Aceite Motor 20W50 1L', 'Lubricantes', 'Castrol', 25, 45, 10, $prov2->id],
            ['REP-002', 'Filtro de Aceite', 'Filtros', 'Fram', 12, 25, 15, $prov2->id],
            ['REP-003', 'Filtro de Aire', 'Filtros', 'Mann', 20, 40, 10, $prov2->id],
            ['REP-004', 'Pastillas de Freno Delanteras', 'Frenos', 'Bosch', 80, 150, 8, $prov3->id],
            ['REP-005', 'Pastillas de Freno Traseras', 'Frenos', 'Bosch', 75, 140, 8, $prov3->id],
            ['REP-006', 'Disco de Freno Delantero', 'Frenos', 'Bosch', 120, 220, 5, $prov3->id],
            ['REP-007', 'Bujia NGK', 'Encendido', 'NGK', 15, 30, 20, $prov1->id],
            ['REP-008', 'Amortiguador Delantero', 'Suspension', 'Monroe', 180, 350, 6, $prov1->id],
            ['REP-009', 'Amortiguador Trasero', 'Suspension', 'Monroe', 160, 320, 6, $prov1->id],
            ['REP-010', 'Correa de Distribucion', 'Motor', 'Gates', 90, 180, 5, $prov1->id],
            ['REP-011', 'Alternador 12V 70A', 'Electrico', 'Bosch', 400, 700, 3, $prov1->id],
            ['REP-012', 'Bateria 60Ah', 'Electrico', 'LTH', 350, 600, 4, $prov1->id],
            ['REP-013', 'Filtro de Combustible', 'Filtros', 'Mann', 18, 35, 12, $prov2->id],
            ['REP-014', 'Liquido de Frenos 1L', 'Lubricantes', 'Castrol', 22, 45, 10, $prov2->id],
            ['REP-015', 'Refrigerante 1L', 'Lubricantes', 'Shell', 18, 35, 15, $prov2->id],
            ['REP-016', 'Kit de Embrague', 'Motor', 'Valeo', 500, 900, 3, $prov1->id],
            ['REP-017', 'Sensor de Oxigeno', 'Electrico', 'Bosch', 120, 250, 5, $prov1->id],
            ['REP-018', 'Limpiaparabrisas Jgo', 'Carroceria', 'Bosch', 35, 70, 15, $prov1->id],
            ['REP-019', 'Foco LED H4', 'Electrico', 'Philips', 25, 55, 20, $prov1->id],
            ['REP-020', 'Aceite Transmision ATF 1L', 'Lubricantes', 'Castrol', 30, 55, 8, $prov2->id],
        ];
        $repuestos = [];
        foreach ($repuestosData as $r) {
            $repuestos[] = Repuesto::firstOrCreate(
                ['codigo' => $r[0]],
                [
                    'nombre' => $r[1],
                    'categoria' => $r[2],
                    'marca' => $r[3],
                    'costo_compra' => $r[4],
                    'precio_venta' => $r[5],
                    'stock_minimo' => $r[6],
                    'proveedor_id' => $r[7],
                    'tipo' => 'repuesto',
                    'estado' => true,
                ]
            );
        }

        $this->command->info('Creando inventario...');
        foreach ($repuestos as $rep) {
            Inventario::firstOrCreate(
                ['sucursal_id' => $sucursal->id, 'repuesto_id' => $rep->id],
                [
                    'cantidad_actual' => rand(10, 50),
                    'cantidad_reservada' => 0,
                    'fecha_actualizacion' => now(),
                ]
            );
        }

        $this->command->info('Creando clientes...');
        $clientesData = [
            ['Juan Carlos Mamani', '45678901', '3-7001001', 'juancarlos@gmail.com', 'Calle Bolivar #123'],
            ['Ana Maria Rojas', '56789012', '3-7001002', 'anamaria@hotmail.com', 'Av. Irala #456'],
            ['Pedro Pablo Quispe', '67890123', '3-7001003', 'pedroquispe@gmail.com', 'Calle Sucre #789'],
            ['Maria Elena Vargas', '78901234', '3-7001004', 'mariaelena@yahoo.com', 'Av. San Martin #321'],
            ['Roberto Carlos Ruiz', '89012345', '3-7001005', 'robertoruiz@gmail.com', 'Calle 21 de Mayo #654'],
            ['Carmen Lourdes Flores', '90123456', '3-7001006', 'carmenflores@gmail.com', 'Av. Busch #987'],
            ['Diego Armando Castillo', '01234567', '3-7001007', 'diego.castillo@outlook.com', 'Calle La Paz #147'],
            ['Sofia Alejandra Rios', '11234567', '3-7001008', 'sofia.rios@gmail.com', 'Av. Ejercito Nacional #258'],
            ['Jose Luis Gutierrez', '12234567', '3-7001009', 'joseluis@hotmail.com', 'Calle Arenales #369'],
            ['Patricia Fernanda Vargas', '13234567', '3-7001010', 'patriciavargas@gmail.com', 'Av. Monseñor Rivero #159'],
        ];
        $clientes = [];
        foreach ($clientesData as $c) {
            $clientes[] = Cliente::firstOrCreate(
                ['ci' => $c[1]],
                [
                    'nombre_completo' => $c[0],
                    'telefono' => $c[2],
                    'email' => $c[3],
                    'direccion' => $c[4],
                    'fecha_registro' => now()->subDays(rand(30, 365)),
                    'estado' => true,
                ]
            );
        }

        $this->command->info('Creando vehiculos...');
        $vehiculosData = [
            ['ABC-123', 'Toyota', 'Corolla', 2018, 'Blanco', '8XABCDEFG12345678', 85000],
            ['DEF-456', 'Suzuki', 'Swift', 2020, 'Rojo', '9YBCDEFGH23456789', 35000],
            ['GHI-789', 'Nissan', 'Sentra', 2019, 'Gris', '7ZCDEFGHI34567890', 62000],
            ['JKL-012', 'Toyota', 'Hilux', 2021, 'Negro', '6ADEFGHIJ45678901', 28000],
            ['MNO-345', 'Honda', 'Civic', 2017, 'Azul', '5BDEFGHIJK56789012', 92000],
            ['PQR-678', 'Mitsubishi', 'Montero', 2020, 'Plateado', '4CDEFGHIJL67890123', 45000],
            ['STU-901', 'Suzuki', 'Grand Vitara', 2022, 'Verde', '3DEFGHIJKM78901234', 15000],
            ['VWX-234', 'Toyota', 'Yaris', 2021, 'Blanco', '2EFGHIJKLN89012345', 22000],
            ['YZA-567', 'Nissan', 'Frontier', 2019, 'Gris Oscuro', '1FGHIJKLMO90123456', 78000],
            ['BCD-890', 'Honda', 'CR-V', 2023, 'Plateado', '0GHIJKLMNP01234567', 8000],
        ];
        $vehiculos = [];
        foreach ($vehiculosData as $i => $v) {
            $cliente = $clientes[$i % count($clientes)];
            $vehiculos[] = Vehiculo::firstOrCreate(
                ['placa' => $v[0]],
                [
                    'cliente_id' => $cliente->id,
                    'marca' => $v[1],
                    'modelo' => $v[2],
                    'anio' => $v[3],
                    'color' => $v[4],
                    'numero_chasis' => $v[5],
                    'kilometraje_actual' => $v[6],
                    'estado' => true,
                ]
            );
        }

        $this->command->info('Creando citas pasadas y futuras...');
        $hoy = Carbon::today();

        $citasData = [
            [$clientes[0], $vehiculos[0], 'Mantenimiento', 'Cambio de aceite y filtro', $hoy->subDays(5), '09:00', $servicios[0]->id ?? null, 'atendida', 'confirmada'],
            [$clientes[1], $vehiculos[1], 'Reparacion', 'Frenos hacen ruido', $hoy->subDays(3), '10:30', $servicios[3]->id ?? null, 'atendida', 'confirmada'],
            [$clientes[2], $vehiculos[2], 'Diagnostico', 'Se enciende luz de check engine', $hoy->subDays(1), '08:00', $servicios[4]->id ?? null, 'confirmada', 'pendiente'],
            [$clientes[3], $vehiculos[3], 'Mantenimiento', 'Alineacion y balanceo', $hoy->addDays(1), '14:00', $servicios[1]->id ?? null, 'pendiente', 'pendiente'],
            [$clientes[4], $vehiculos[4], 'Reparacion', 'Aire acondicionado no enfria', $hoy->addDays(2), '09:30', $servicios[10]->id ?? null, 'pendiente', 'pendiente'],
            [$clientes[5], $vehiculos[5], 'Revision', 'Revision general antes de viaje', $hoy->addDays(3), '11:00', $servicios[5]->id ?? null, 'pendiente', 'pendiente'],
            [$clientes[0], $vehiculos[0], 'Reparacion', 'Suspension trasera ruidosa', $hoy->subDays(10), '08:30', $servicios[18]->id ?? null, 'cancelada', 'cancelada'],
            [$clientes[6], $vehiculos[6], 'Mantenimiento', 'Cambio de pastillas de freno', $hoy->subDays(2), '15:00', $servicios[3]->id ?? null, 'atendida', 'confirmada'],
            [$clientes[7], $vehiculos[7], 'Diagnostico', 'Auto no enciende bien en las mananas', $hoy->subDays(7), '09:00', $servicios[4]->id ?? null, 'atendida', 'confirmada'],
            [$clientes[8], $vehiculos[8], 'Reparacion', 'Embrague patina', $hoy->addDays(5), '10:00', $servicios[19]->id ?? null, 'pendiente', 'pendiente'],
        ];

        $citas = [];
        foreach ($citasData as $c) {
            $citas[] = Cita::create([
                'cliente_id' => $c[0]->id,
                'vehiculo_id' => $c[1]->id,
                'sucursal_id' => $sucursal->id,
                'usuario_id' => ($userRecepcion ? $userRecepcion->id : ($userAdmin->id ?? 1)),
                'servicio_id' => $c[6],
                'fecha' => $c[4],
                'hora' => $c[5],
                'tipo' => $c[2],
                'descripcion_problema' => $c[3],
                'estado' => $c[7],
                'estado_anterior' => null,
                'deja_vehiculo' => rand(0, 1),
                'costo_consulta' => 0,
            ]);
        }

        $this->command->info('Creando ordenes de trabajo...');
        $ordenesData = [
            [$clientes[0], $vehiculos[0], $hoy->subDays(5), 'Cambio de aceite y filtro programado', 'finalizada', 80, 45, 0, 125, $citas[0] ?? null],
            [$clientes[1], $vehiculos[1], $hoy->subDays(3), 'Ruido en frenos delanteros al frenar', 'finalizada', 150, 160, 10, 300, $citas[1] ?? null],
            [$clientes[8], $vehiculos[8], $hoy->subDays(12), 'Perdida de potencia y vibracion', 'en_proceso', 0, 450, 0, 450, null],
            [$clientes[3], $vehiculos[3], $hoy->subDays(1), 'Alineacion y balanceo de las 4 ruedas', 'diagnostico', 120, 0, 0, 120, null],
            [$clientes[6], $vehiculos[6], $hoy->subDays(2), 'Cambio de pastillas y discos de freno', 'finalizada', 150, 440, 20, 570, $citas[7] ?? null],
            [$clientes[7], $vehiculos[7], $hoy->subDays(7), 'Dificultad para encender en frio', 'finalizada', 200, 250, 0, 450, $citas[8] ?? null],
            [$clientes[4], $vehiculos[4], $hoy->subDays(1), 'Aire acondicionado no enfria lo suficiente', 'recibida', 400, 0, 0, 400, $citas[4] ?? null],
            [$clientes[9], $vehiculos[9], $hoy->subDays(3), 'Revision de 5000 km', 'finalizada', 100, 105, 0, 205, null],
        ];

        $ordenes = [];
        foreach ($ordenesData as $i => $o) {
            $numOrden = 'OT-' . str_pad((string) ($i + 1), 6, '0', STR_PAD_LEFT);
            $fechaEmision = $o[2];
            $fechaInicio = in_array($o[4], ['en_proceso', 'finalizada', 'diagnostico']) ? (clone $fechaEmision)->addHours(1) : null;
            $fechaFin = in_array($o[4], ['finalizada']) ? (clone $fechaEmision)->addDays(1) : null;

            $ordenes[] = OrdenTrabajo::create([
                'numero_orden' => $numOrden,
                'cliente_id' => $o[0]->id,
                'vehiculo_id' => $o[1]->id,
                'sucursal_id' => $sucursal->id,
                'usuario_recepcion_id' => $userRecepcion->id ?? ($userAdmin->id ?? 1),
                'cita_id' => $o[9]?->id,
                'fecha_emision' => $fechaEmision,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'kilometraje_ingreso' => rand(5000, 100000),
                'descripcion_problema' => $o[3],
                'estado' => $o[4],
                'subtotal_servicios' => $o[5],
                'subtotal_repuestos' => $o[6],
                'descuento' => $o[7],
                'total_general' => $o[8],
            ]);
        }

        $this->command->info('Creando detalles de ordenes de trabajo...');
        $detallesData = [
            [$ordenes[0], 'servicio', $servicios[0]->id, null, 'Cambio de aceite 20W50 + filtro', 1, 80, 80],
            [$ordenes[0], 'repuesto', null, $repuestos[0]->id, 'Aceite Motor 20W50 1L', 1, 45, 45],
            [$ordenes[0], 'repuesto', null, $repuestos[1]->id, 'Filtro de Aceite', 1, 25, 25],
            [$ordenes[1], 'servicio', $servicios[3]->id, null, 'Cambio de pastillas de freno delanteras', 1, 150, 150],
            [$ordenes[1], 'repuesto', null, $repuestos[4]->id, 'Pastillas de Freno Delanteras', 1, 150, 150],
            [$ordenes[1], 'repuesto', null, $repuestos[3]->id, 'Pastillas de Freno Traseras', 1, 150, 150],
            [$ordenes[2], 'servicio', $servicios[19]->id, null, 'Cambio de embrague completo', 1, 800, 800],
            [$ordenes[3], 'servicio', $servicios[1]->id, null, 'Alineacion y balanceo', 1, 120, 120],
            [$ordenes[4], 'servicio', $servicios[3]->id, null, 'Cambio de pastillas de freno', 1, 150, 150],
            [$ordenes[4], 'repuesto', null, $repuestos[4]->id, 'Pastillas de Freno Delanteras Bosch', 1, 150, 150],
            [$ordenes[4], 'repuesto', null, $repuestos[5]->id, 'Discos de Freno Delanteros', 2, 220, 440],
            [$ordenes[5], 'servicio', $servicios[4]->id, null, 'Diagnostico computarizado', 1, 200, 200],
            [$ordenes[5], 'repuesto', null, $repuestos[16]->id, 'Sensor de Oxigeno Bosch', 1, 250, 250],
            [$ordenes[6], 'servicio', $servicios[10]->id, null, 'Revision y reparacion de A/A', 1, 400, 400],
            [$ordenes[7], 'servicio', $servicios[5]->id, null, 'Revision general', 1, 100, 100],
            [$ordenes[7], 'repuesto', null, $repuestos[2]->id, 'Filtro de Aire', 1, 40, 40],
            [$ordenes[7], 'repuesto', null, $repuestos[12]->id, 'Filtro de Combustible', 1, 35, 35],
        ];

        foreach ($detallesData as $d) {
            DetalleOrdenTrabajo::create([
                'orden_trabajo_id' => $d[0]->id,
                'tipo' => $d[1],
                'servicio_id' => $d[2],
                'repuesto_id' => $d[3],
                'descripcion' => $d[4],
                'cantidad' => $d[5],
                'precio_unitario' => $d[6],
                'subtotal' => $d[7],
            ]);
        }

        $this->command->info('Creando pagos...');
        $metodos = MetodoPago::all()->keyBy('nombre');
        $efectivo = $metodos->get('Efectivo');
        $qr = $metodos->get('QR');
        $tarjeta = $metodos->get('Tarjeta');

        $pagosData = [
            [$ordenes[0], $efectivo->id, $hoy->subDays(5), 125, 'COMP-001', 'Pago en efectivo'],
            [$ordenes[1], $tarjeta->id, $hoy->subDays(3), 300, 'COMP-002', 'Pago con tarjeta'],
            [$ordenes[4], $efectivo->id, $hoy->subDays(2), 570, 'COMP-003', 'Pago en efectivo'],
            [$ordenes[5], $qr->id, $hoy->subDays(7), 450, 'COMP-004', 'Pago con QR'],
            [$ordenes[7], $efectivo->id, $hoy->subDays(3), 205, 'COMP-005', 'Pago en efectivo'],
            [$ordenes[0], $qr->id, $hoy->subDays(5), 125, 'COMP-006', null],
            [$ordenes[1], $efectivo->id, $hoy->subDays(3), 300, 'COMP-007', null],
        ];

        $pagos = [];
        foreach ($pagosData as $p) {
            $pagos[] = Pago::create([
                'orden_trabajo_id' => $p[0]->id,
                'metodo_pago_id' => $p[1],
                'usuario_id' => $userAdmin->id ?? 1,
                'fecha_pago' => $p[2],
                'monto' => $p[3],
                'numero_comprobante' => $p[4],
                'referencia' => $p[5] ?? 'Pago por ' . ($p[1] == $efectivo?->id ? 'efectivo' : ($p[1] == $qr?->id ? 'QR' : 'tarjeta')),
                'estado' => 'confirmado',
            ]);
        }

        $this->command->info('Creando comprobantes...');
        foreach ($pagos as $i => $p) {
            if ($i >= 5) break;
            $orden = $p->ordenTrabajo;
            $cliente = $orden->cliente;
            Comprobante::create([
                'pago_id' => $p->id,
                'cliente_id' => $cliente->id,
                'numero' => 'FACT-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'fecha_emision' => $p->fecha_pago,
                'nit_ci' => $cliente->ci,
                'razon_social' => $cliente->nombre_completo,
                'monto_total' => $p->monto,
                'estado' => 'emitido',
            ]);
        }

        $this->command->info('========================================');
        $this->command->info('DATOS DE PRUEBA CREADOS EXITOSAMENTE');
        $this->command->info('========================================');
        $this->command->warn('Usuarios para pruebas:');
        $this->command->warn('  admin / TallerPro2026! (Administrador)');
        $this->command->warn('  gerente / TallerPro2026! (Gerente)');
        $this->command->warn('  recepcion / TallerPro2026! (Recepcionista)');
        $this->command->warn('  mecanico1 / TallerPro2026! (Mecanico)');
        $this->command->warn('  mecanico2 / TallerPro2026! (Mecanico)');
        $this->command->info('========================================');
    }
}
