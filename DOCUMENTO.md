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

## 6. Próximos pasos (roadmap)

1. CRUD de Usuarios
2. CRUD de Empleados → Mecánicos
3. CRUD de Roles y asignación de permisos
4. CRUD de Sucursales
5. Módulo de Clientes y Vehículos
6. Módulo de Citas
7. Módulo de Órdenes de Trabajo
8. Módulo de Repuestos e Inventario
9. Módulo de Pagos y Facturación
10. Auditoría y Reportes
