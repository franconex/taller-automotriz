# Taller Automotriz - Sistema de Gestión

Sistema web para la gestión integral de un taller automotriz. Desarrollado con Laravel 12.

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

## Campo Administrador

El usuario administrador (`admin`) tiene el rol **Administrador** con todos los permisos del sistema habilitados. Las rutas del panel administrador están protegidas bajo el middleware `rol:Administrador` y se encuentran en el prefijo `/admin/*`. Cualquier usuario sin este rol no puede acceder a estas rutas, sin importar que esté autenticado.

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

# Iniciar servidor de desarrollo
php artisan serve
```

## Estructura

- **Autenticación:** Login con username/email y contraseña. Al iniciar sesión, redirige al dashboard según el rol.
- **Roles y Permisos:** Control de acceso basado en roles (RBAC) con permisos granulares por acción.
- **Panel Administrador:** CRUD completo de todos los recursos del sistema.
- **Auditoría:** Registro de todas las acciones importantes realizadas en el sistema.

> ⚠️ **Importante:** Cambiar la contraseña `TallerPro2026!` en producción.
