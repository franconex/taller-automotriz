# TALLER PRO — Sistema de Gestión Automotriz

## 1. Idea del proyecto

Sistema web para administrar un taller automotriz. Permite gestionar
usuarios, empleados, roles, permisos, sucursales, clientes, vehículos,
citas, órdenes de trabajo, repuestos, inventario, proveedores,
facturación y auditoría, todo con control de acceso por roles.

## 2. Stack tecnológico

| Capa       | Tecnología                        |
|------------|-----------------------------------|
| Backend    | Laravel 13 (PHP 8.4)             |
| Frontend   | Blade + Tailwind CSS 3 + Alpine.js|
| Base datos | MySQL                             |
| Build      | Vite 8                            |

## 3. Base de datos — 24 tablas

### Seguridad y acceso

**roles**
| Campo | Tipo |
|-------|------|
| idRol | INT (PK) |
| nombreRol | VARCHAR(50) |
| descripcion | VARCHAR(200) |
| fechaCreacion | DATETIME |

**permisos**
| Campo | Tipo |
|-------|------|
| idPermiso | INT (PK) |
| nombrePermiso | VARCHAR(100) |
| codigo | VARCHAR(50) |
| modulo | VARCHAR(50) |
| descripcion | VARCHAR(200) |

**rol_permiso**
| Campo | Tipo |
|-------|------|
| idRol | INT (PK, FK) |
| idPermiso | INT (PK, FK) |

**usuarios**
| Campo | Tipo |
|-------|------|
| idUsuario | INT (PK) |
| nombreUsuario | VARCHAR(50) |
| email | VARCHAR(100) |
| contraseniaHash | VARCHAR(255) |
| idRol | INT (FK) |
| idSucursal | INT (FK) |
| estado | BOOLEAN |
| fechaRegistro | DATETIME |
| ultimoAcceso | DATETIME |

**auditoria**
| Campo | Tipo |
|-------|------|
| idAuditoria | INT (PK) |
| idUsuario | INT (FK) |
| accion | VARCHAR(50) |
| entidadAfectada | VARCHAR(50) |
| idEntidad | INT |
| detalleCambio | TEXT |
| fechaAccion | DATETIME |
| direccionIP | VARCHAR(45) |

---

### Sucursales

**sucursales**
| Campo | Tipo |
|-------|------|
| idSucursal | INT (PK) |
| nombre | VARCHAR(100) |
| direccion | VARCHAR(200) |
| telefono | VARCHAR(20) |
| horarioAtencion | VARCHAR(100) |
| estado | BOOLEAN |

---

### Clientes y vehículos

**clientes**
| Campo | Tipo |
|-------|------|
| idCliente | INT (PK) |
| nombre | VARCHAR(50) |
| apellido | VARCHAR(50) |
| ci | VARCHAR(20) |
| telefono | VARCHAR(20) |
| email | VARCHAR(100) |
| direccion | VARCHAR(200) |
| fechaRegistro | DATETIME |
| estado | BOOLEAN |

**marca_vehiculos**
| Campo | Tipo |
|-------|------|
| idMarca | INT (PK) |
| nombreMarca | VARCHAR(50) |
| paisOrigen | VARCHAR(50) |

**modelo_vehiculos**
| Campo | Tipo |
|-------|------|
| idModelo | INT (PK) |
| nombreModelo | VARCHAR(50) |
| anioLanzamiento | INT |
| idMarca | INT (FK) |

**vehiculos**
| Campo | Tipo |
|-------|------|
| idVehiculo | INT (PK) |
| placa | VARCHAR(20) |
| anio | INT |
| color | VARCHAR(30) |
| numeroChasis | VARCHAR(50) |
| kilometrajeActual | INT |
| idCliente | INT (FK) |
| idModelo | INT (FK) |

---

### Servicios y especialidades

**tipo_servicios**
| Campo | Tipo |
|-------|------|
| idTipoServicio | INT (PK) |
| nombreTipo | VARCHAR(50) |
| descripcion | VARCHAR(200) |

**servicios**
| Campo | Tipo |
|-------|------|
| idServicio | INT (PK) |
| nombreServicio | VARCHAR(100) |
| descripcion | TEXT |
| precioBase | DECIMAL(10,2) |
| duracionEstimada | INT |
| idTipoServicio | INT (FK) |
| estado | BOOLEAN |

**especialidades**
| Campo | Tipo |
|-------|------|
| idEspecialidad | INT (PK) |
| nombreEspecialidad | VARCHAR(50) |
| descripcion | VARCHAR(200) |

---

### Mecánicos

**mecanicos**
| Campo | Tipo |
|-------|------|
| idMecanico | INT (PK) |
| nombre | VARCHAR(50) |
| apellido | VARCHAR(50) |
| ci | VARCHAR(20) |
| telefono | VARCHAR(20) |
| email | VARCHAR(100) |
| fechaIngreso | DATE |
| idEspecialidad | INT (FK) |
| idSucursal | INT (FK) |
| estado | BOOLEAN |

---

### Órdenes de trabajo

**ordenes_trabajo**
| Campo | Tipo |
|-------|------|
| idOrden | INT (PK) |
| fechaEmision | DATETIME |
| fechaInicio | DATETIME |
| fechaFin | DATETIME |
| estado | VARCHAR(20) |
| observaciones | TEXT |
| totalGeneral | DECIMAL(10,2) |
| idCliente | INT (FK) |
| idVehiculo | INT (FK) |
| idMecanico | INT (FK) |
| idSucursal | INT (FK) |
| idUsuario | INT (FK) |

**detalle_orden_trabajo**
| Campo | Tipo |
|-------|------|
| idDetalle | INT (PK) |
| idOrden | INT (FK) |
| tipoDetalle | VARCHAR(20) |
| idReferencia | INT |
| cantidad | INT |
| precioUnitario | DECIMAL(10,2) |
| subtotal | DECIMAL(10,2) |
| observaciones | TEXT |

---

### Repuestos e inventario

**proveedores**
| Campo | Tipo |
|-------|------|
| idProveedor | INT (PK) |
| nombreEmpresa | VARCHAR(100) |
| contacto | VARCHAR(100) |
| telefono | VARCHAR(20) |
| email | VARCHAR(100) |
| direccion | VARCHAR(200) |
| nit | VARCHAR(30) |
| tiempoEntrega | INT |
| estado | BOOLEAN |

**repuestos**
| Campo | Tipo |
|-------|------|
| idRepuesto | INT (PK) |
| codigo | VARCHAR(50) |
| nombre | VARCHAR(100) |
| descripcion | TEXT |
| precioUnitario | DECIMAL(10,2) |
| stockMinimo | INT |
| idProveedor | INT (FK) |

**inventario**
| Campo | Tipo |
|-------|------|
| idInventario | INT (PK) |
| idRepuesto | INT (FK) |
| idSucursal | INT (FK) |
| cantidadActual | INT |
| fechaUltimaActualizacion | DATETIME |

**movimientos_inventario**
| Campo | Tipo |
|-------|------|
| idMovimiento | INT (PK) |
| idInventario | INT (FK) |
| tipoMovimiento | VARCHAR(20) |
| cantidad | INT |
| fechaMovimiento | DATETIME |
| motivo | VARCHAR(200) |
| idUsuario | INT (FK) |
| idOrden | INT (FK) |

---

### Pagos y facturación

**metodos_pago**
| Campo | Tipo |
|-------|------|
| idMetodoPago | INT (PK) |
| nombreMetodo | VARCHAR(50) |
| descripcion | VARCHAR(200) |
| activo | BOOLEAN |

**pagos**
| Campo | Tipo |
|-------|------|
| idPago | INT (PK) |
| fechaPago | DATETIME |
| monto | DECIMAL(10,2) |
| idMetodoPago | INT (FK) |
| idOrden | INT (FK) |
| numeroComprobante | VARCHAR(50) |
| estado | VARCHAR(20) |
| idUsuario | INT (FK) |

**facturas**
| Campo | Tipo |
|-------|------|
| idFactura | INT (PK) |
| numeroFactura | VARCHAR(50) |
| idPago | INT (FK) |
| idCliente | INT (FK) |
| fechaEmision | DATETIME |
| nit | VARCHAR(30) |
| razonSocial | VARCHAR(100) |
| montoTotal | DECIMAL(10,2) |
| codigoControl | VARCHAR(100) |
| estado | VARCHAR(20) |

---

### Citas

**citas**
| Campo | Tipo |
|-------|------|
| idCita | INT (PK) |
| idCliente | INT (FK) |
| idVehiculo | INT (FK) |
| fechaCita | DATE |
| horaCita | TIME |
| tipo | VARCHAR(20) |
| descripcionProblema | TEXT |
| estado | VARCHAR(20) |
| dejaVehiculo | BOOLEAN |
| costoConsulta | DECIMAL(10,2) |
| observaciones | TEXT |
| idUsuario | INT (FK) |

---

## 4. Roles del sistema

| Rol | Acceso |
|-----|--------|
| Administrador | Total del sistema |
| Gerente | Reportes y estadísticas |
| Recepcionista | Clientes, citas, órdenes |
| Mecánico | Órdenes de trabajo asignadas |

## 5. Lo que ya está implementado

- [x] Landing page pública con servicios y formulario de cita vía WhatsApp
- [x] Autenticación (login, logout, recuperación de contraseña)
- [x] Middleware de verificación de rol
- [x] Dashboard para cada rol con layout admin unificado
- [x] Sidebar con navegación e indicadores "Próximamente"
- [x] Diseño responsive con Tailwind CSS
- [x] Base de datos con migraciones (sucursales, roles, usuarios, permisos, empleados)

## 6. Sistema de permisos y navegación

### 6.1 Arquitectura

```
Usuario → Rol → Permisos (tabla permiso_rol)
```

- Cada usuario tiene **un solo rol**.
- Cada rol tiene **muchos permisos**.
- No se asignan permisos directamente a usuarios.
- Los permisos se gestionan desde el panel: Admin → Roles → Editar → Asignar permisos.

### 6.2 Códigos de permisos

Formato: `modulo.accion`

```
clientes.ver
clientes.crear
clientes.editar
clientes.desactivar

ordenes.ver
ordenes.crear
ordenes.asignar
ordenes.actualizar_estado
ordenes.registrar_diagnostico

citas.ver
citas.crear
citas.editar
citas.confirmar
citas.reprogramar
citas.cancelar

usuarios.ver
usuarios.crear
usuarios.editar
usuarios.desactivar
usuarios.restablecer_password

empleados.ver
empleados.crear
empleados.editar
empleados.desactivar

roles.ver
roles.crear
roles.editar
roles.asignar_permisos

permisos.ver
permisos.crear
permisos.editar

sucursales.ver
sucursales.crear
sucursales.editar
sucursales.desactivar

auditoria.ver
reportes.ver
```

### 6.3 Cómo funciona el menú (sidebar)

El menú lateral está en `resources/views/components/admin/sidebar.blade.php`.

Cada opción del menú se muestra u oculta con `@can('permiso.accion')`:

```blade
@can('clientes.ver')
    <x-admin.nav-link href="{{ route('admin.clientes.index') }}" ...>
        Clientes
    </x-admin.nav-link>
@endcan
```

**El menú NO se genera solo.** Cuando se crea un módulo nuevo, hay que:

1. Crear el permiso en `PermisoSeeder`.
2. Crear rutas + controlador + vistas.
3. Agregar `@can('modulo.ver')` en el `sidebar.blade.php`.
4. Asignar el permiso a los roles desde el panel.

Una vez agregado, el sistema funciona automático: cada usuario ve solo los módulos que su rol tiene permitidos.

### 6.4 Menú completo del Administrador

| Sección | Módulo | Permiso | ¿Qué hace? |
|---|---|---|---|
| **Dashboard** | Inicio | — | Panel con indicadores, gráficos, actividad |
| **Personal** | | | |
| | Empleados | `empleados.ver` | CRUD empleados + creación de cuenta de usuario |
| | Usuarios | `usuarios.ver` | CRUD cuentas de acceso, cambio de rol, contraseña |
| **Seguridad** | | | |
| | Roles | `roles.ver` | CRUD roles + matriz de asignación de permisos |
| | Auditoría | `auditoria.ver` | Solo lectura — registro de actividad del sistema |
| **Gestión** | | | |
| | Clientes | `clientes.ver` | CRUD clientes del taller |
| | Sucursales | `sucursales.ver` | CRUD sucursales |
| | Especialidades | `especialidades.ver` | Catálogo de especialidades |
| | Tipos de Servicio | `tipo_servicios.ver` | Catálogo tipos de servicio |
| | Métodos de Pago | `metodos_pago.ver` | Catálogo métodos de pago |
| **Reportes** | | | |
| | Reportes | `reportes.ver` | Reportes (próximamente) |

### 6.5 Agregar un módulo nuevo (paso a paso)

Ejemplo: crear el módulo **Citas**.

**1. Crear modelo y migración**

```bash
php artisan make:model Cita -m
```

**2. Crear controlador**

```bash
php artisan make:controller Admin/CitaController --resource
```

**3. Agregar permisos al seeder**

En `database/seeders/PermisoSeeder.php`:

```php
['Citas', 'citas.ver', 'Ver citas', 'Ver listado de citas'],
['Citas', 'citas.crear', 'Crear citas', 'Crear nuevas citas'],
['Citas', 'citas.editar', 'Editar citas', 'Editar citas existentes'],
['Citas', 'citas.confirmar', 'Confirmar citas', 'Confirmar citas'],
```

```bash
php artisan db:seed --class=PermisoSeeder
```

**4. Crear rutas**

En `routes/web.php`, dentro del grupo `admin` (después de la sección con `rol:Administrador`, con `permiso:citas.ver`):

```php
Route::middleware(['permiso:citas.ver'])->group(function () {
    Route::resource('citas', CitaController::class);
});
```

**5. Agregar al menú lateral**

En `resources/views/components/admin/sidebar.blade.php`:

```blade
@can('citas.ver')
    <x-admin.nav-link href="{{ route('admin.citas.index') }}"
        :active="request()->routeIs('admin.citas.*')"
        icon="calendar">
        Citas
    </x-admin.nav-link>
@endcan
```

**6. Asignar permisos a roles**

Desde el panel: Admin → Roles → Editar (rol) → marcar permisos → Guardar.

### 6.6 Verificar permisos en controladores

```php
// Directamente en el controlador
if (! $request->user()->tienePermiso('citas.confirmar')) {
    abort(403);
}

// Con middleware en la ruta
Route::middleware(['permiso:citas.crear']);

// Con Policy
$this->authorize('create', Cita::class);
```

### 6.7 Verificar permisos en vistas (Blade)

```blade
@can('citas.editar')
    <a href="{{ route('admin.citas.edit', $cita) }}">Editar</a>
@endcan

@can('citas.confirmar')
    <button>Confirmar cita</button>
@endcan
```

### 6.8 Middlewares disponibles

| Alias | Clase | Archivo | Uso |
|---|---|---|---|
| `rol` | `RolMiddleware` | `app/Http/Middleware/RolMiddleware.php` | Restringe por nombre de rol |
| `permiso` | `PermisoMiddleware` | `app/Http/Middleware/PermisoMiddleware.php` | Restringe por código de permiso |

Ambos están registrados en `bootstrap/app.php`:

```php
$middleware->alias([
    'rol' => RolMiddleware::class,
    'permiso' => PermisoMiddleware::class,
]);
```

### 6.9 Permisos por rol (asignación inicial)

Los roles vienen con permisos pre-asignados por el `RolPermisoSeeder`:

| Rol | Permisos asignados |
|---|---|
| **Administrador** | Todos (acceso completo vía `Gate::before`) |
| **Gerente** | `clientes.ver`, `empleados.ver`, `ordenes.ver`, `pagos.ver`, `reportes.ver`, `sucursales.ver`, `auditoria.ver` |
| **Recepcionista** | `clientes.*`, `vehiculos.*`, `citas.*`, `ordenes.ver`, `ordenes.crear`, `pagos.ver`, `pagos.registrar` |
| **Mecánico** | `ordenes.ver`, `ordenes.actualizar_estado`, `ordenes.registrar_diagnostico` |

### 6.10 Tablas del sistema de permisos

| Tabla | Función |
|---|---|
| `roles` | Catálogo de roles del sistema |
| `permisos` | Catálogo de permisos con código único |
| `permiso_rol` | Asignación de permisos a roles (tabla pivote) |

### 6.11 Relaciones en los modelos

**User.php**
```php
public function rol(): BelongsTo
public function empleado(): HasOne
public function tieneRol(string $rol): bool
public function tienePermiso(string $codigo): bool
```

**Rol.php**
```php
public function users(): HasMany
public function permisos(): BelongsToMany
```

**permisos.php**
```php
public function roles(): BelongsToMany
```

## 7. Próximos pasos (roadmap)

1. CRUD de Usuarios
2. CRUD de Empleados
3. CRUD de Roles y asignación de permisos
4. CRUD de Sucursales
5. Módulo de Clientes y Vehículos
6. Módulo de Citas
7. Módulo de Órdenes de Trabajo
8. Módulo de Repuestos e Inventario
9. Módulo de Pagos y Facturación
10. Auditoría y Reportes
