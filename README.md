# Taller Automotriz - Sistema de Gestión

Sistema web para la gestión integral de un taller automotriz. Desarrollado con Laravel 13.

## Roles del Sistema

| Rol | Descripción |
|-----|-------------|
| **Administrador** | Acceso completo al sistema. Gestiona usuarios, roles, permisos, sucursales, empleados y toda la configuración del sistema. |
| **Gerente** | Supervisión general, reportes, autorizaciones, auditoría, gestión de inventario y pagos. |
| **Recepcionista** | Atención al cliente, registro de clientes/vehículos, gestión de citas y creación de órdenes de trabajo. |
| **Mecánico** | Visualización de órdenes asignadas, actualización de estado de servicios y consulta de inventario. |

## Credenciales de Prueba (Seeders)

Todos los usuarios comparten la contraseña: **`TallerPro2026!`**

### Administrador
| Campo | Valor |
|-------|-------|
| Usuario | `admin` |
| Email | `admin@tallerpro.com` |
| Rol | Administrador |

### Gerente
| Campo | Valor |
|-------|-------|
| Usuario | `gerente` |
| Email | `gerente@tallerpro.com` |
| Rol | Gerente |

### Recepcionista
| Campo | Valor |
|-------|-------|
| Usuario | `recepcion` |
| Email | `recepcion@tallerpro.com` |
| Rol | Recepcionista |

### Mecánico
| Campo | Valor |
|-------|-------|
| Usuario | `mecanico` |
| Email | `mecanico@tallerpro.com` |
| Rol | Mecánico |

## Funcionalidades del Sistema

### Módulo de Citas (Calendario Interactivo)
- Calendario FullCalendar con vistas **Día**, **Semana** y **Mes**
- Vista semanal predeterminada con horario de 08:00 a 19:00
- **Mini calendario** lateral para navegación rápida de fechas
- Filtros por sucursal, servicio, mecánico y estado
- Leyenda de estados con colores (Confirmada, Pendiente, Atendida, Cancelada, No asistió)
- **Agendar cita** seleccionando un espacio en el calendario (precarga fecha y hora)
- Auto-asignación de hora más cercana disponible (redondeo a 30 min)
- **Detalle de cita** modal con acciones contextuales según estado
- Reprogramar, confirmar, cancelar, marcar no asistió, convertir a orden
- Validación de cruces de horario (vehículo y mecánico)
- Tabla "Citas del Día" debajo del calendario
- Lista de "Próximas Citas" en columna lateral
- Solo mecánicos **disponibles** se muestran en el formulario
- Validación client-side: no permite fechas/horas pasadas

### Perfil de Usuario
- Los usuarios heredan automáticamente datos del empleado (nombre, email, rol, sucursal)
- El formulario de creación de usuario solo pide **empleado + username + password**
- Página **"Mi perfil"** con foto, datos del empleado (solo lectura), username editable y cambio de contraseña
- La foto se muestra en el navbar en lugar de las iniciales

### Empleados y Usuarios
- Los empleados tienen un **rol predefinido** (Administrador, Gerente, Recepcionista, Mecánico)
- Un rol puede tener **muchos empleados** (relación 1:N)
- Al asignar rol "Mecánico", se piden especialidad y disponibilidad
- Al cambiar el rol de Mecánico a otro, se elimina el registro de mecánico
- Al dar de baja un empleado (toggle), su **usuario** también se desactiva automáticamente
- Un usuario con empleado inactivo **no puede iniciar sesión**

### Métodos de Pago
- Solo 3 métodos fijos: **Efectivo** (no editable), **Tarjeta** (editable), **QR** (editable)
- No se pueden crear ni eliminar métodos de pago
- Efectivo no se puede desactivar

## Requisitos

- PHP 8.3+
- Composer
- Node.js & npm
- MySQL/MariaDB o SQLite

## Instalación

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/taller-automotriz.git
cd taller-automotriz

# Instalar dependencias PHP
composer install

# Instalar dependencias frontend
npm install && npm run build

# Copiar y configurar variables de entorno
copy .env.example .env
# Editar .env con tus credenciales de base de datos

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Crear enlace simbólico para almacenamiento
php artisan storage:link

# Iniciar servidor de desarrollo
php artisan serve
```

## Estructura

- **Autenticación:** Login con username/email y contraseña. Al iniciar sesión, redirige al dashboard según el rol.
- **Roles y Permisos:** Control de acceso basado en roles (RBAC) con permisos granulares por acción.
- **Panel Administrador:** CRUD completo de todos los recursos del sistema.
- **Auditoría:** Registro de todas las acciones importantes realizadas en el sistema.
- **Perfil:** Cada usuario puede ver y editar su perfil desde el navbar.
- **Citas:** Calendario interactivo con FullCalendar, filtros y gestión completa.

> ⚠️ **Importante:** Cambiar la contraseña `TallerPro2026!` en producción.
