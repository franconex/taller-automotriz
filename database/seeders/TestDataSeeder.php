<?php

namespace Database\Seeders;

use App\Models\AsignacionTrabajo;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\DetalleOrdenTrabajo;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\Inventario;
use App\Models\Mecanico;
use App\Models\MetodoPago;
use App\Models\MovimientoInventario;
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
        $password = 'TallerPro2026!';

        $adminRole = Rol::where('nombre', 'Administrador')->first();
        $gerenteRole = Rol::where('nombre', 'Gerente')->first();
        $recepcionistaRole = Rol::where('nombre', 'Recepcionista')->first();
        $mecanicoRole = Rol::where('nombre', 'Mecánico')->first();

        $this->command->info('=== CREANDO 3 SUCURSALES ===');

        $sucursalesData = [
            [
                'nombre' => 'Sucursal Principal',
                'direccion' => 'Av. Cristo Redentor #1250, Santa Cruz',
                'telefono' => '3-3456789',
                'horario_atencion' => 'Lun-Vie 7:00-19:00, Sáb 7:00-14:00',
                'latitud' => -17.783333,
                'longitud' => -63.182500,
            ],
            [
                'nombre' => 'Sucursal Norte Montero',
                'direccion' => 'Av. Ejército Nacional #450, Montero',
                'telefono' => '3-9221100',
                'horario_atencion' => 'Lun-Vie 8:00-18:00, Sáb 8:00-13:00',
                'latitud' => -17.333333,
                'longitud' => -63.250000,
            ],
            [
                'nombre' => 'Sucursal Sur La Guardia',
                'direccion' => 'Carretera Santa Cruz-Cotoca Km 12, La Guardia',
                'telefono' => '3-7711222',
                'horario_atencion' => 'Lun-Vie 8:00-17:00, Sáb 8:00-12:00',
                'latitud' => -17.933333,
                'longitud' => -63.133333,
            ],
        ];

        $sucursales = [];
        foreach ($sucursalesData as $sd) {
            $sucursales[] = Sucursal::firstOrCreate(
                ['nombre' => $sd['nombre']],
                $sd
            );
        }

        [$sucPrincipal, $sucNorte, $sucSur] = $sucursales;

        $this->command->info('=== CREANDO ESPECIALIDADES ===');
        $espMeca = Especialidad::firstOrCreate(['nombre' => 'Mecánica General'], ['descripcion' => 'Mecánica general vehicular', 'estado' => true]);
        $espElec = Especialidad::firstOrCreate(['nombre' => 'Electricidad Automotriz'], ['descripcion' => 'Electricidad y electrónica automotriz', 'estado' => true]);
        $espMotor = Especialidad::firstOrCreate(['nombre' => 'Motores Diesel'], ['descripcion' => 'Especialización en motores diesel', 'estado' => true]);
        $espAire = Especialidad::firstOrCreate(['nombre' => 'Aire Acondicionado'], ['descripcion' => 'Reparación de sistemas de climatización', 'estado' => true]);
        $espDiag = Especialidad::firstOrCreate(['nombre' => 'Diagnóstico Avanzado'], ['descripcion' => 'Diagnóstico computarizado y escáner', 'estado' => true]);

        $this->command->info('=== CREANDO EMPLEADOS POR SUCURSAL ===');

        $userAdmin = User::where('username', 'admin')->first();

        $empleadosPorSucursal = [];
        $usersPorSucursal = [];

        $staffConfig = [
            $sucPrincipal->id => [
                'gerente' => ['Carlos Gutiérrez', '28475610', '3-7002001', 'carlos.gutierrez@tallerpro.com', 'Gerente Sucursal', '2024-01-15'],
                'recepcion' => ['Ana Belén Roca', '37582910', '3-7002002', 'ana.roca@tallerpro.com', 'Recepcionista', '2024-02-20'],
                'mecanicos' => [
                    ['Miguel Ángel Suárez', '19283746', '3-7002003', 'miguel.suarez@tallerpro.com', 'Mecánico Senior', '2024-01-10', $espMeca->id],
                    ['Roberto Vargas', '28374655', '3-7002004', 'roberto.vargas@tallerpro.com', 'Electricista Automotriz', '2024-03-05', $espElec->id],
                    ['Hugo Fernández', '37482910', '3-7002005', 'hugo.fernandez@tallerpro.com', 'Especialista en Motores', '2024-04-12', $espMotor->id],
                ],
            ],
            $sucNorte->id => [
                'gerente' => ['María José Ribera', '48592017', '3-9222010', 'maria.ribera@tallerpro.com', 'Gerente Sucursal', '2024-06-01'],
                'recepcion' => ['Pedro Luis Montes', '59683028', '3-9222011', 'pedro.montes@tallerpro.com', 'Recepcionista', '2024-06-15'],
                'mecanicos' => [
                    ['Jorge Antonio Ríos', '60794139', '3-9222012', 'jorge.rios@tallerpro.com', 'Mecánico General', '2024-07-01', $espMeca->id],
                    ['Daniel Eduardo Paz', '71805240', '3-9222013', 'daniel.paz@tallerpro.com', 'Diagnosticador', '2024-08-20', $espDiag->id],
                ],
            ],
            $sucSur->id => [
                'gerente' => ['Fernando Méndez', '82916351', '3-7711223', 'fernando.mendez@tallerpro.com', 'Gerente Sucursal', '2025-01-10'],
                'recepcion' => ['Laura Beatriz Castro', '93027462', '3-7711224', 'laura.castro@tallerpro.com', 'Recepcionista', '2025-01-15'],
                'mecanicos' => [
                    ['Sergio Ramiro Quispe', '04138573', '3-7711225', 'sergio.quispe@tallerpro.com', 'Mecánico General', '2025-02-01', $espMeca->id],
                    ['Ricardo Antonio López', '15249684', '3-7711226', 'ricardo.lopez@tallerpro.com', 'Aire Acondicionado', '2025-02-15', $espAire->id],
                ],
            ],
        ];

        foreach ($staffConfig as $sucId => $config) {
            $gerenteEmpleado = Empleado::firstOrCreate(
                ['ci' => $config['gerente'][1]],
                [
                    'sucursal_id' => $sucId,
                    'rol_id' => $gerenteRole->id,
                    'nombre_completo' => $config['gerente'][0],
                    'telefono' => $config['gerente'][2],
                    'email' => $config['gerente'][3],
                    'cargo' => $config['gerente'][4],
                    'fecha_contratacion' => $config['gerente'][5],
                    'estado' => true,
                ]
            );

            $recepcionEmpleado = Empleado::firstOrCreate(
                ['ci' => $config['recepcion'][1]],
                [
                    'sucursal_id' => $sucId,
                    'rol_id' => $recepcionistaRole->id,
                    'nombre_completo' => $config['recepcion'][0],
                    'telefono' => $config['recepcion'][2],
                    'email' => $config['recepcion'][3],
                    'cargo' => $config['recepcion'][4],
                    'fecha_contratacion' => $config['recepcion'][5],
                    'estado' => true,
                ]
            );

            $empleadosPorSucursal[$sucId] = [
                'gerente' => $gerenteEmpleado,
                'recepcion' => $recepcionEmpleado,
                'mecanicos' => [],
            ];

            $sucName = Sucursal::find($sucId)->nombre;
            $sufijo = match ($sucId) { $sucPrincipal->id => 'sp', $sucNorte->id => 'sn', $sucSur->id => 'ss', default => 'sx' };

            $gerenteUser = User::updateOrCreate(
                ['username' => "gerente_{$sufijo}"],
                [
                    'nombre' => $gerenteEmpleado->nombre_completo,
                    'email' => "gerente.{$sufijo}@tallerpro.com",
                    'password' => Hash::make($password),
                    'estado' => 'activo',
                    'rol_id' => $gerenteRole->id,
                    'sucursal_id' => $sucId,
                    'empleado_id' => $gerenteEmpleado->id,
                ]
            );

            $recepcionUser = User::updateOrCreate(
                ['username' => "recepcion_{$sufijo}"],
                [
                    'nombre' => $recepcionEmpleado->nombre_completo,
                    'email' => "recepcion.{$sufijo}@tallerpro.com",
                    'password' => Hash::make($password),
                    'estado' => 'activo',
                    'rol_id' => $recepcionistaRole->id,
                    'sucursal_id' => $sucId,
                    'empleado_id' => $recepcionEmpleado->id,
                ]
            );

            $usersPorSucursal[$sucId] = [
                'gerente' => $gerenteUser,
                'recepcion' => $recepcionUser,
                'mecanicos' => [],
            ];

            foreach ($config['mecanicos'] as $i => $m) {
                $empleado = Empleado::firstOrCreate(
                    ['ci' => $m[1]],
                    [
                        'sucursal_id' => $sucId,
                        'rol_id' => $mecanicoRole->id,
                        'nombre_completo' => $m[0],
                        'telefono' => $m[2],
                        'email' => $m[3],
                        'cargo' => $m[4],
                        'fecha_contratacion' => $m[5],
                        'estado' => true,
                    ]
                );

                $mecUser = User::updateOrCreate(
                    ['username' => "mecanico_{$sufijo}_" . ($i + 1)],
                    [
                        'nombre' => $empleado->nombre_completo,
                        'email' => "mecanico.{$sufijo}" . ($i + 1) . "@tallerpro.com",
                        'password' => Hash::make($password),
                        'estado' => 'activo',
                        'rol_id' => $mecanicoRole->id,
                        'sucursal_id' => $sucId,
                        'empleado_id' => $empleado->id,
                    ]
                );

                $mecanico = Mecanico::firstOrCreate(
                    ['empleado_id' => $empleado->id],
                    [
                        'especialidad_id' => $m[6],
                        'disponibilidad' => $i === 0 ? 'ocupado' : 'disponible',
                        'observaciones' => $m[4],
                    ]
                );

                $empleadosPorSucursal[$sucId]['mecanicos'][] = $empleado;
                $usersPorSucursal[$sucId]['mecanicos'][] = $mecUser;

                $this->command->info("  Creado {$m[0]} en " . Sucursal::find($sucId)->nombre);
            }
        }

        $this->command->info('=== USUARIOS BASE SIN EMPLEADO ASIGNADO ===');
        if ($userAdmin) {
            $userAdmin->update(['empleado_id' => null, 'sucursal_id' => null]);
        }

        $userGerente = User::where('username', 'gerente')->first();
        if ($userGerente) {
            $userGerente->update(['empleado_id' => null, 'sucursal_id' => null]);
        }

        $userRecepcion = User::where('username', 'recepcion')->first();
        if ($userRecepcion) {
            $userRecepcion->update(['empleado_id' => null, 'sucursal_id' => null]);
        }

        $userMecanico = User::where('username', 'mecanico')->first();
        if ($userMecanico) {
            $userMecanico->update(['empleado_id' => null, 'sucursal_id' => null]);
        }

        $this->command->info('=== CREANDO PROVEEDORES ===');
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
                'contacto' => 'Ana Laura Suárez',
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
                'contacto' => 'Mario Suárez',
                'telefono' => '3-4567890',
                'email' => 'pedidos@frenosabc.com',
                'direccion' => 'Av. Grigotá #750',
                'estado' => true,
            ]
        );
        $prov4 = Proveedor::firstOrCreate(
            ['nit' => '408765432'],
            [
                'nombre_empresa' => 'Autopartes del Oriente',
                'contacto' => 'Carmen Llanos',
                'telefono' => '3-5678901',
                'email' => 'ventas@autopartesori.com',
                'direccion' => 'Av. Paraguá #420',
                'estado' => true,
            ]
        );

        $this->command->info('=== CREANDO TIPOS Y SERVICIOS ===');
        $tiposServicio = [];
        $tiposData = [
            ['Mantenimiento Preventivo', 'Mantenimiento programado por kilometraje'],
            ['Mecánica General', 'Reparaciones mecánicas generales'],
            ['Electricidad', 'Sistemas eléctricos y electrónicos'],
            ['Revisión y Diagnóstico', 'Diagnóstico y escaneo computarizado'],
            ['Latonería y Pintura', 'Trabajos de carrocería y pintura'],
            ['Aire Acondicionado', 'Sistemas de climatización automotriz'],
            ['Transmisión y Embrague', 'Cajas de cambio y sistemas de transmisión'],
        ];
        foreach ($tiposData as $td) {
            $tiposServicio[] = TipoServicio::firstOrCreate(
                ['nombre' => $td[0]],
                ['descripcion' => $td[1], 'estado' => true]
            );
        }

        $serviciosData = [
            ['Cambio de Aceite y Filtro', $tiposServicio[0]->id, 80, 30, 'Cambio de aceite de motor + filtro nuevo'],
            ['Alineación y Balanceo', $tiposServicio[1]->id, 120, 45, 'Alineación computarizada y balanceo dinámico'],
            ['Revisión de Frenos', $tiposServicio[1]->id, 60, 20, 'Inspección visual y medición de pastillas y discos'],
            ['Cambio de Pastillas de Freno', $tiposServicio[1]->id, 150, 60, 'Reemplazo de pastillas delanteras o traseras'],
            ['Diagnóstico Computarizado', $tiposServicio[3]->id, 200, 45, 'Escaneo electrónico completo del vehículo'],
            ['Revisión General 30 Puntos', $tiposServicio[3]->id, 100, 30, 'Checklist de 30 puntos de inspección'],
            ['Cambio de Bujías', $tiposServicio[1]->id, 90, 40, 'Reemplazo de bujías y revisión de bobinas'],
            ['Reparación de Motor de Arranque', $tiposServicio[2]->id, 250, 90, 'Diagnóstico y reparación del motor de arranque'],
            ['Instalación de Alarma', $tiposServicio[2]->id, 180, 60, 'Instalación de alarma con cierre centralizado'],
            ['Cambio de Alternador', $tiposServicio[2]->id, 120, 50, 'Reemplazo de alternador y correa'],
            ['Carga de Gas A/A', $tiposServicio[5]->id, 350, 60, 'Recarga de gas refrigerante R134a'],
            ['Reparación de Aire Acondicionado', $tiposServicio[5]->id, 500, 120, 'Diagnóstico y reparación completa del sistema A/A'],
            ['Pintura Completa', $tiposServicio[4]->id, 2500, 480, 'Lijado, masillado y pintura completa'],
            ['Reparación de Parachoques', $tiposServicio[4]->id, 400, 120, 'Reparación y pintura de parachoques'],
            ['Latonería Menor', $tiposServicio[4]->id, 300, 90, 'Reparación de golpes menores y abolladuras'],
            ['Cambio de Amortiguadores', $tiposServicio[1]->id, 200, 60, 'Reemplazo de amortiguadores delanteros o traseros'],
            ['Lavado de Inyectores', $tiposServicio[1]->id, 150, 45, 'Limpieza ultrasónica de inyectores'],
            ['Cambio de Correa de Distribución', $tiposServicio[1]->id, 500, 180, 'Reemplazo de correa de distribución + tensores'],
            ['Escaneo Electrónico', $tiposServicio[3]->id, 150, 30, 'Lectura de códigos de falla OBD2'],
            ['Revisión de Suspensión', $tiposServicio[1]->id, 80, 30, 'Inspección de amortiguadores, resortes y rótulas'],
            ['Cambio de Embrague', $tiposServicio[6]->id, 800, 240, 'Reemplazo de kit de embrague completo'],
            ['Cambio de Aceite de Transmisión', $tiposServicio[6]->id, 120, 40, 'Cambio de aceite de transmisión automática o manual'],
            ['Mantenimiento 10.000 km', $tiposServicio[0]->id, 350, 120, 'Cambio de aceite, filtros, revisión general, rotación de llantas'],
            ['Mantenimiento 20.000 km', $tiposServicio[0]->id, 550, 180, 'Mantenimiento completo + bujías + líquidos'],
            ['Diagnóstico de Motor Diesel', $tiposServicio[3]->id, 300, 60, 'Diagnóstico especializado para motores diesel comunes'],
        ];
        $servicios = [];
        foreach ($serviciosData as $s) {
            $servicios[] = Servicio::firstOrCreate(
                ['nombre' => $s[0], 'tipo_servicio_id' => $s[1]],
                [
                    'descripcion' => $s[4],
                    'precio_base' => $s[2],
                    'duracion_estimada_minutos' => $s[3],
                    'estado' => true,
                ]
            );
        }

        $this->command->info('=== CREANDO REPUESTOS ===');
        $repuestosData = [
            ['REP-001', 'Aceite Motor 20W50 1L', 'Lubricantes y Aceites', 'Castrol', 25, 45, 10, $prov2->id],
            ['REP-002', 'Filtro de Aceite', 'Filtros', 'Fram', 12, 25, 15, $prov2->id],
            ['REP-003', 'Filtro de Aire', 'Filtros', 'Mann', 20, 40, 10, $prov2->id],
            ['REP-004', 'Pastillas de Freno Delanteras', 'Frenos', 'Bosch', 80, 150, 8, $prov3->id],
            ['REP-005', 'Pastillas de Freno Traseras', 'Frenos', 'Bosch', 75, 140, 8, $prov3->id],
            ['REP-006', 'Disco de Freno Delantero', 'Frenos', 'Bosch', 120, 220, 5, $prov3->id],
            ['REP-007', 'Bujía NGK Iridium', 'Sistema Eléctrico', 'NGK', 25, 45, 20, $prov1->id],
            ['REP-008', 'Amortiguador Delantero', 'Suspensión y Dirección', 'Monroe', 180, 350, 6, $prov1->id],
            ['REP-009', 'Amortiguador Trasero', 'Suspensión y Dirección', 'Monroe', 160, 320, 6, $prov1->id],
            ['REP-010', 'Correa de Distribución', 'Motor y Transmisión', 'Gates', 90, 180, 5, $prov1->id],
            ['REP-011', 'Alternador 12V 70A', 'Sistema Eléctrico', 'Bosch', 400, 750, 3, $prov4->id],
            ['REP-012', 'Batería 60Ah LTH', 'Sistema Eléctrico', 'LTH', 350, 600, 4, $prov4->id],
            ['REP-013', 'Filtro de Combustible', 'Filtros', 'Mann', 18, 35, 12, $prov2->id],
            ['REP-014', 'Líquido de Frenos DOT4 1L', 'Frenos', 'Castrol', 22, 45, 10, $prov2->id],
            ['REP-015', 'Refrigerante 50/50 1L', 'Motor y Transmisión', 'Shell', 18, 35, 15, $prov2->id],
            ['REP-016', 'Kit de Embrague', 'Motor y Transmisión', 'Valeo', 500, 950, 3, $prov1->id],
            ['REP-017', 'Sensor de Oxígeno', 'Sistema Eléctrico', 'Bosch', 120, 250, 5, $prov4->id],
            ['REP-018', 'Limpiaparabrisas Jgo', 'Carrocería y Accesorios', 'Bosch', 35, 70, 15, $prov1->id],
            ['REP-019', 'Foco LED H4', 'Sistema Eléctrico', 'Philips', 25, 55, 20, $prov4->id],
            ['REP-020', 'Aceite Transmisión ATF 1L', 'Motor y Transmisión', 'Castrol', 30, 55, 8, $prov2->id],
            ['REP-021', 'Gas Refrigerante R134a 1Kg', 'Aire Acondicionado', 'Chemours', 80, 150, 5, $prov4->id],
            ['REP-022', 'Compresor de Aire Acondicionado', 'Aire Acondicionado', 'Sanden', 800, 1400, 2, $prov4->id],
            ['REP-023', 'Rótula de Suspensión', 'Suspensión y Dirección', 'Moog', 60, 120, 8, $prov1->id],
            ['REP-024', 'Terminal de Dirección', 'Suspensión y Dirección', 'Moog', 45, 90, 10, $prov1->id],
            ['REP-025', 'Aceite Motor Diésel 15W40 1L', 'Motor y Transmisión', 'Shell', 28, 50, 12, $prov2->id],
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

        $this->command->info('=== CREANDO INVENTARIO POR SUCURSAL ===');
        $inventarioPorSucursal = [];

        $stockPorSucursal = [
            $sucPrincipal->id => [
                'altos' => [0, 1, 2, 3, 6, 12, 14, 18, 19, 23],
                'medios' => [4, 5, 7, 8, 9, 10, 13, 15, 20, 24],
                'bajos' => [11, 16, 17, 21, 22],
            ],
            $sucNorte->id => [
                'altos' => [0, 1, 2, 3, 4, 12, 13, 14, 24],
                'medios' => [5, 6, 7, 9, 10, 15, 17, 20],
                'bajos' => [8, 11, 16, 18, 19, 21, 22, 23],
            ],
            $sucSur->id => [
                'altos' => [0, 1, 2, 3, 6, 12, 13, 14, 18, 19],
                'medios' => [4, 7, 8, 10, 15, 17, 20, 23, 24],
                'bajos' => [5, 9, 11, 16, 21, 22],
            ],
        ];

        foreach ($sucursales as $suc) {
            $config = $stockPorSucursal[$suc->id];
            $sucRepuestos = [];

            foreach ($repuestos as $idx => $rep) {
                $cantidad = 0;
                if (in_array($idx, $config['altos'])) {
                    $cantidad = rand(25, 60);
                } elseif (in_array($idx, $config['medios'])) {
                    $cantidad = rand(8, 20);
                } elseif (in_array($idx, $config['bajos'])) {
                    $cantidad = rand(1, 5);
                } else {
                    $cantidad = rand(10, 30);
                }

                $inv = Inventario::firstOrCreate(
                    ['sucursal_id' => $suc->id, 'repuesto_id' => $rep->id],
                    [
                        'cantidad_actual' => $cantidad,
                        'cantidad_reservada' => rand(0, min(3, $cantidad)),
                        'costo_promedio' => $rep->costo_compra,
                        'fecha_actualizacion' => now(),
                    ]
                );
                $sucRepuestos[] = $inv;
            }
            $inventarioPorSucursal[$suc->id] = $sucRepuestos;
            $this->command->info("  Inventario creado para {$suc->nombre}");
        }

        $this->command->info('=== CREANDO CLIENTES ===');
        $clientesData = [
            ['Juan Carlos Mamani', '45678901', '3-7001001', 'juancarlos.mamani@gmail.com', 'Calle Bolívar #123, Santa Cruz'],
            ['Ana María Rojas', '56789012', '3-7001002', 'anamaria.rojas@hotmail.com', 'Av. Irala #456, Santa Cruz'],
            ['Pedro Pablo Quispe', '67890123', '3-7001003', 'pedro.quispe@gmail.com', 'Calle Sucre #789, Montero'],
            ['María Elena Vargas', '78901234', '3-7001004', 'mariaelena.vargas@yahoo.com', 'Av. San Martín #321, Santa Cruz'],
            ['Roberto Carlos Ruiz', '89012345', '3-7001005', 'roberto.ruiz@gmail.com', 'Calle 21 de Mayo #654, Montero'],
            ['Carmen Lourdes Flores', '90123456', '3-7001006', 'carmen.flores@gmail.com', 'Av. Busch #987, Santa Cruz'],
            ['Diego Armando Castillo', '01234567', '3-7001007', 'diego.castillo@outlook.com', 'Calle La Paz #147, La Guardia'],
            ['Sofía Alejandra Ríos', '11234567', '3-7001008', 'sofia.rios@gmail.com', 'Av. Ejército Nacional #258, Montero'],
            ['José Luis Gutiérrez', '12234567', '3-7001009', 'joseluis.gutierrez@hotmail.com', 'Calle Arenales #369, Santa Cruz'],
            ['Patricia Fernanda Vargas', '13234567', '3-7001010', 'patricia.vargas@gmail.com', 'Av. Monseñor Rivero #159, La Guardia'],
            ['Ricardo Antonio Morales', '14234567', '3-7001011', 'ricardo.morales@gmail.com', 'Calle La Paz #456, La Guardia'],
            ['Gabriela Andrea Paredes', '15234567', '3-7001012', 'gabriela.paredes@gmail.com', 'Barrio Jardín, Montero'],
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

        $this->command->info('=== CREANDO VEHÍCULOS ===');
        $vehiculosData = [
            ['ABC-123', 'Toyota', 'Corolla', 2018, 'Blanco', '8XABCDEFG12345678', 85000, 0],
            ['DEF-456', 'Suzuki', 'Swift', 2020, 'Rojo', '9YBCDEFGH23456789', 35000, 1],
            ['GHI-789', 'Nissan', 'Sentra', 2019, 'Gris', '7ZCDEFGHI34567890', 62000, 2],
            ['JKL-012', 'Toyota', 'Hilux', 2021, 'Negro', '6ADEFGHIJ45678901', 28000, 3],
            ['MNO-345', 'Honda', 'Civic', 2017, 'Azul', '5BDEFGHIJK56789012', 92000, 4],
            ['PQR-678', 'Mitsubishi', 'Montero', 2020, 'Plateado', '4CDEFGHIJL67890123', 45000, 5],
            ['STU-901', 'Suzuki', 'Grand Vitara', 2022, 'Verde', '3DEFGHIJKM78901234', 15000, 6],
            ['VWX-234', 'Toyota', 'Yaris', 2021, 'Blanco', '2EFGHIJKLN89012345', 22000, 7],
            ['YZA-567', 'Nissan', 'Frontier', 2019, 'Gris Oscuro', '1FGHIJKLMO90123456', 78000, 8],
            ['BCD-890', 'Honda', 'CR-V', 2023, 'Plateado', '0GHIJKLMNP01234567', 8000, 9],
            ['EFG-111', 'Toyota', 'Land Cruiser', 2021, 'Blanco Perla', '9HIJKLMNOQ12345678', 18000, 10],
            ['HIJ-222', 'Volkswagen', 'Amarok', 2022, 'Gris', '8IJKLMNOPR23456789', 12000, 11],
        ];
        $vehiculos = collect();
        foreach ($vehiculosData as $i => $v) {
            $cliente = $clientes[$v[7]];
            $vehiculos->push(Vehiculo::firstOrCreate(
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
            ));
        }

        $this->command->info('=== CREANDO CITAS POR SUCURSAL ===');
        $hoy = Carbon::today();
        $todasLasCitas = [];
        $horas = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'];

        $citasConfig = [
            $sucPrincipal->id => [
                'offsets' => [-5, -3, -1, 0, 1, 2, 3],
                'por_dia' => 3,
                'user' => $usersPorSucursal[$sucPrincipal->id]['recepcion'],
            ],
            $sucNorte->id => [
                'offsets' => [-4, -2, 0, 1, 3, 5],
                'por_dia' => 2,
                'user' => $usersPorSucursal[$sucNorte->id]['recepcion'],
            ],
            $sucSur->id => [
                'offsets' => [-3, -1, 0, 2, 4],
                'por_dia' => 2,
                'user' => $usersPorSucursal[$sucSur->id]['recepcion'],
            ],
        ];

        $idx = 0;
        foreach ($citasConfig as $sucId => $cfg) {
            $userRec = $cfg['user'];
            foreach ($cfg['offsets'] as $offset) {
                $fecha = $hoy->copy()->addDays($offset);
                for ($i = 0; $i < $cfg['por_dia']; $i++) {
                    $cliente = $clientes[$idx % count($clientes)];
                    $vehiculo = $vehiculos->where('cliente_id', $cliente->id)->first() ?? $vehiculos->first();
                    $tipo = ['diagnostico', 'mantenimiento', 'reparacion'][array_rand(['diagnostico', 'mantenimiento', 'reparacion'])];
                    $servicioId = $tipo === 'diagnostico' ? 5 : ($tipo === 'mantenimiento' ? 1 : 3);
                    $estado = $offset < 0 ? 'atendida' : (rand(0, 2) > 0 ? 'confirmada' : 'pendiente');

                    $cita = Cita::create([
                        'cliente_id' => $cliente->id,
                        'vehiculo_id' => $vehiculo->id,
                        'sucursal_id' => $sucId,
                        'usuario_id' => $userRec->id,
                        'servicio_id' => $servicioId,
                        'fecha' => $fecha,
                        'hora' => $horas[array_rand($horas)],
                        'tipo' => $tipo,
                        'descripcion_problema' => "Cita de {$tipo} - {$cliente->nombre_completo}",
                        'estado' => $estado,
                        'deja_vehiculo' => rand(0, 1),
                        'costo_consulta' => 0,
                    ]);
                    $todasLasCitas[] = $cita;
                    $idx++;
                }
            }
            $this->command->info("  Citas creadas para " . Sucursal::find($sucId)->nombre);
        }

        $this->command->info('=== CREANDO ÓRDENES DE TRABAJO POR SUCURSAL ===');
        $todasLasOrdenes = [];
        $detallesGlobal = [];
        $ordenIdx = 0;

        $ordenesConfig = [
            $sucPrincipal->id => [
                'user' => $usersPorSucursal[$sucPrincipal->id]['recepcion'],
                'data' => [
                    [$clientes[0], $vehiculos[0], -5, 'Cambio de aceite y filtro programado', 'finalizada', 80, 70, 0, 150, 0],
                    [$clientes[1], $vehiculos[1], -3, 'Ruido en frenos delanteros al frenar', 'finalizada', 150, 150, 10, 290, 1],
                    [$clientes[5], $vehiculos[5], -1, 'Aire acondicionado no enfría', 'en_proceso', 500, 150, 0, 650, null],
                    [$clientes[3], $vehiculos[3], 0, 'Alineación y balanceo 4 ruedas', 'diagnostico', 120, 0, 0, 120, 2],
                    [$clientes[8], $vehiculos[8], -12, 'Pérdida de potencia y vibración', 'finalizada', 800, 950, 20, 1730, null],
                ],
                'detalles' => [
                    [0, 'servicio', 0, null, 'Cambio de aceite 20W50 + filtro', 1, 80, 80],
                    [0, 'repuesto', null, 0, 'Aceite Motor 20W50 1L', 1, 45, 45],
                    [0, 'repuesto', null, 1, 'Filtro de Aceite', 1, 25, 25],
                    [1, 'servicio', 3, null, 'Cambio de pastillas de freno delanteras', 1, 150, 150],
                    [1, 'repuesto', null, 3, 'Pastillas de Freno Delanteras', 1, 150, 150],
                    [2, 'servicio', 11, null, 'Diagnóstico y reparación de A/A', 1, 500, 500],
                    [2, 'repuesto', null, 21, 'Gas Refrigerante R134a 1Kg', 1, 150, 150],
                    [3, 'servicio', 1, null, 'Alineación y balanceo', 1, 120, 120],
                    [4, 'servicio', 17, null, 'Cambio de correa de distribución + kit', 1, 500, 500],
                    [4, 'repuesto', null, 9, 'Correa de Distribución', 1, 180, 180],
                    [4, 'servicio', 20, null, 'Cambio de embrague completo', 1, 800, 800],
                    [4, 'repuesto', null, 15, 'Kit de Embrague', 1, 950, 950],
                ],
            ],
            $sucNorte->id => [
                'user' => $usersPorSucursal[$sucNorte->id]['recepcion'],
                'data' => [
                    [$clientes[4], $vehiculos[4], -7, 'Dificultad para encender en frío', 'finalizada', 200, 250, 0, 450, 3],
                    [$clientes[7], $vehiculos[7], -3, 'Revisión 5000 km + cambio de aceite', 'finalizada', 350, 45, 0, 395, 4],
                    [$clientes[2], $vehiculos[2], -2, 'Vibración al acelerar en ruta', 'recibida', 150, 0, 0, 150, 5],
                    [$clientes[11], $vehiculos[11], -1, 'Cambio de aceite de transmisión', 'diagnostico', 120, 0, 0, 120, null],
                ],
                'detalles' => [
                    [0, 'servicio', 4, null, 'Diagnóstico computarizado', 1, 200, 200],
                    [0, 'repuesto', null, 16, 'Sensor de Oxígeno Bosch', 1, 250, 250],
                    [1, 'servicio', 22, null, 'Mantenimiento 10.000 km', 1, 350, 350],
                    [1, 'repuesto', null, 0, 'Aceite Motor 20W50 1L', 1, 45, 45],
                    [2, 'servicio', 4, null, 'Diagnóstico computarizado', 1, 200, 200],
                    [3, 'servicio', 21, null, 'Cambio de aceite de transmisión', 1, 120, 120],
                ],
            ],
            $sucSur->id => [
                'user' => $usersPorSucursal[$sucSur->id]['recepcion'],
                'data' => [
                    [$clientes[6], $vehiculos[6], -2, 'Cambio de pastillas y discos de freno', 'finalizada', 150, 440, 20, 570, 6],
                    [$clientes[9], $vehiculos[9], -3, 'Escaneo electrónico por check engine', 'finalizada', 150, 250, 0, 400, 7],
                    [$clientes[10], $vehiculos[10], -1, 'Revisión de suspensión delantera', 'en_proceso', 80, 120, 0, 200, null],
                ],
                'detalles' => [
                    [0, 'servicio', 3, null, 'Cambio de pastillas de freno', 1, 150, 150],
                    [0, 'repuesto', null, 3, 'Pastillas de Freno Delanteras', 1, 150, 150],
                    [0, 'repuesto', null, 5, 'Discos de Freno Delanteros', 2, 220, 440],
                    [1, 'servicio', 18, null, 'Escaneo electrónico OBD2', 1, 150, 150],
                    [1, 'repuesto', null, 16, 'Sensor de Oxígeno Bosch', 1, 250, 250],
                    [2, 'servicio', 19, null, 'Revisión de suspensión', 1, 80, 80],
                    [2, 'repuesto', null, 22, 'Rótula de Suspensión', 1, 120, 120],
                ],
            ],
        ];

        foreach ($ordenesConfig as $sucId => $cfg) {
            $userRec = $cfg['user'];
            $sucName = Sucursal::find($sucId)->nombre;

            foreach ($cfg['data'] as $oi => $o) {
                $ordenIdx++;
                $numOrden = 'OT-' . str_pad((string) $ordenIdx, 6, '0', STR_PAD_LEFT);
                $fechaEmision = $hoy->copy()->addDays($o[2]);
                $fechaInicio = in_array($o[4], ['en_proceso', 'finalizada', 'diagnostico']) ? (clone $fechaEmision)->addHours(1) : null;
                $fechaFin = $o[4] === 'finalizada' ? (clone $fechaEmision)->addDays(1) : null;
                $citaRef = $o[9] !== null && isset($todasLasCitas[$o[9]]) ? $todasLasCitas[$o[9]]->id : null;

                $orden = OrdenTrabajo::create([
                    'numero_orden' => $numOrden,
                    'cliente_id' => $o[0]->id,
                    'vehiculo_id' => $o[1]->id,
                    'sucursal_id' => $sucId,
                    'usuario_recepcion_id' => $userRec->id,
                    'cita_id' => $citaRef,
                    'fecha_emision' => $fechaEmision,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'kilometraje_ingreso' => $o[1]->kilometraje_actual,
                    'descripcion_problema' => $o[3],
                    'estado' => $o[4],
                    'subtotal_servicios' => $o[5],
                    'subtotal_repuestos' => $o[6],
                    'descuento' => $o[7],
                    'total_general' => $o[8],
                ]);
                $todasLasOrdenes[] = $orden;
            }
            $this->command->info("  Órdenes creadas para {$sucName}");
        }

        $this->command->info('=== CREANDO DETALLES DE ÓRDENES ===');
        $ordenGlobalIdx = 0;
        foreach ($ordenesConfig as $sucId => $cfg) {
            foreach ($cfg['detalles'] as $d) {
                $ordenRelativaIdx = $d[0];
                $ordenGlobal = $ordenGlobalIdx + $ordenRelativaIdx;
                $orden = $todasLasOrdenes[$ordenGlobal] ?? null;
                if (!$orden) continue;

                $servicioId = $d[2] !== null ? $servicios[$d[2]]->id : null;
                $repuestoId = $d[3] !== null ? $repuestos[$d[3]]->id : null;

                DetalleOrdenTrabajo::create([
                    'orden_trabajo_id' => $orden->id,
                    'tipo' => $d[1],
                    'servicio_id' => $servicioId,
                    'repuesto_id' => $repuestoId,
                    'descripcion' => $d[4],
                    'cantidad' => $d[5],
                    'precio_unitario' => $d[6],
                    'subtotal' => $d[7],
                ]);
            }
            $ordenGlobalIdx += count($cfg['data']);
        }

        $this->command->info('=== CREANDO PAGOS ===');
        $metodos = MetodoPago::all()->keyBy('nombre');
        $efectivo = $metodos->get('Efectivo');
        $qr = $metodos->get('QR');
        $tarjeta = $metodos->get('Tarjeta');

        $pagosConfig = [
            ['orden_idx' => 0, 'metodo' => $efectivo->id, 'dias_resta' => 5, 'monto' => 150, 'comprobante' => 'COMP-001'],
            ['orden_idx' => 1, 'metodo' => $tarjeta->id, 'dias_resta' => 3, 'monto' => 290, 'comprobante' => 'COMP-002'],
            ['orden_idx' => 4, 'metodo' => $efectivo->id, 'dias_resta' => 2, 'monto' => 570, 'comprobante' => 'COMP-003'],
            ['orden_idx' => 5, 'metodo' => $qr->id, 'dias_resta' => 7, 'monto' => 450, 'comprobante' => 'COMP-004'],
            ['orden_idx' => 6, 'metodo' => $efectivo->id, 'dias_resta' => 3, 'monto' => 395, 'comprobante' => 'COMP-005'],
            ['orden_idx' => 8, 'metodo' => $qr->id, 'dias_resta' => 2, 'monto' => 570, 'comprobante' => 'COMP-006'],
            ['orden_idx' => 9, 'metodo' => $tarjeta->id, 'dias_resta' => 3, 'monto' => 400, 'comprobante' => 'COMP-007'],
        ];

        $pagos = [];
        foreach ($pagosConfig as $pc) {
            $orden = $todasLasOrdenes[$pc['orden_idx']] ?? null;
            if (!$orden) continue;

            $pagos[] = Pago::create([
                'orden_trabajo_id' => $orden->id,
                'metodo_pago_id' => $pc['metodo'],
                'usuario_id' => $userAdmin->id ?? 1,
                'fecha_pago' => $hoy->copy()->subDays($pc['dias_resta']),
                'monto' => $pc['monto'],
                'numero_comprobante' => $pc['comprobante'],
                'referencia' => 'Pago por ' . match ($pc['metodo']) { $efectivo?->id => 'efectivo', $qr?->id => 'QR', default => 'tarjeta' },
                'estado' => 'confirmado',
            ]);
        }

        $this->command->info('=== CREANDO COMPROBANTES ===');
        foreach ($pagos as $i => $p) {
            $orden = $p->ordenTrabajo;
            $cliente = $orden->cliente;
            Comprobante::firstOrCreate(
                ['numero' => 'FACT-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT)],
                [
                    'pago_id' => $p->id,
                    'cliente_id' => $cliente->id,
                    'fecha_emision' => $p->fecha_pago,
                    'nit_ci' => $cliente->ci,
                    'razon_social' => $cliente->nombre_completo,
                    'monto_total' => $p->monto,
                    'estado' => 'emitido',
                ]
            );
        }

        $this->command->info('=== CREANDO MOVIMIENTOS DE INVENTARIO ===');
        foreach ($inventarioPorSucursal as $sucId => $inventarios) {
            foreach (array_slice($inventarios, 0, 3) as $inv) {
                MovimientoInventario::firstOrCreate(
                    [
                        'inventario_id' => $inv->id,
                        'tipo' => 'entrada_inicial',
                        'fecha_movimiento' => now()->subDays(30),
                    ],
                    [
                        'usuario_id' => $userAdmin->id ?? 1,
                        'cantidad' => $inv->cantidad_actual,
                        'existencia_anterior' => 0,
                        'existencia_nueva' => $inv->cantidad_actual,
                        'motivo' => 'Inventario inicial',
                    ]
                );
            }
        }

        $this->command->info('=== CREANDO ASIGNACIONES ===');
        $asignacionesCount = 0;
        foreach ($ordenesConfig as $sucId => $cfg) {
            $ordenIdxStart = ($sucId === $sucPrincipal->id ? 0 : ($sucId === $sucNorte->id ? 5 : 9));
            $mecs = $usersPorSucursal[$sucId]['mecanicos'];

            foreach ($cfg['data'] as $li => $o) {
                if ($o[4] === 'recibida') continue;
                $ordenGlobalIdx2 = $ordenIdxStart + $li;
                $orden = $todasLasOrdenes[$ordenGlobalIdx2] ?? null;
                if (!$orden || empty($mecs)) continue;

                $mec = $mecs[array_rand($mecs)];
                $mecanico = Mecanico::where('empleado_id', $mec->empleado_id)->first();
                if (!$mecanico) continue;

                $fechaAsig = $orden->fecha_emision->copy()->addHour();

                AsignacionTrabajo::create([
                    'orden_trabajo_id' => $orden->id,
                    'mecanico_id' => $mecanico->id,
                    'usuario_asignador_id' => $usersPorSucursal[$sucId]['recepcion']->id,
                    'actividad_asignada' => $o[3],
                    'prioridad' => $o[4] === 'en_proceso' ? 'alta' : 'normal',
                    'estado' => $o[4] === 'finalizada' ? 'finalizada' : ($o[4] === 'en_proceso' ? 'en_proceso' : 'pendiente'),
                    'fecha_asignacion' => $fechaAsig,
                    'fecha_inicio' => $o[4] === 'finalizada' || $o[4] === 'en_proceso' ? $fechaAsig : null,
                    'fecha_finalizacion' => $o[4] === 'finalizada' ? $fechaAsig->addHours(rand(2, 8)) : null,
                ]);
                $asignacionesCount++;
            }
        }
        $this->command->info("  {$asignacionesCount} asignaciones creadas");

        $this->command->info('========================================');
        $this->command->info('DATOS DE PRUEBA CREADOS EXITOSAMENTE');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->warn('USUARIOS PARA PRUEBAS (password: TallerPro2026!)');
        $this->command->warn('──────────────────────────────────────────────');
        $this->command->warn('  admin                  - Administrador (sin sucursal fija)');
        $this->command->warn('  gerente_sp             - Gerente Sucursal Principal');
        $this->command->warn('  gerente_sn             - Gerente Sucursal Norte Montero');
        $this->command->warn('  gerente_ss             - Gerente Sucursal Sur La Guardia');
        $this->command->warn('  recepcion_sp           - Recepcionista Suc. Principal');
        $this->command->warn('  recepcion_sn           - Recepcionista Suc. Norte');
        $this->command->warn('  recepcion_ss           - Recepcionista Suc. Sur');
        $this->command->warn('  mecanico_sp_1/2/3      - Mecánicos Suc. Principal');
        $this->command->warn('  mecanico_sn_1/2        - Mecánicos Suc. Norte');
        $this->command->warn('  mecanico_ss_1/2        - Mecánicos Suc. Sur');
        $this->command->warn('  cliente                - Cliente de Prueba');
        $this->command->warn('');
        $this->command->warn('ADMIN también puede acceder como:');
        $this->command->warn('  admin / TallerPro2026!');
        $this->command->info('========================================');
    }
}
