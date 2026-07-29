<?php

namespace App\Providers;

use App\Models\Autorizacion;
use App\Models\Cita;
use App\Models\Comprobante;
use App\Models\EstimacionOrden;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\Subservicio;
use App\Models\Vehiculo;
use App\Policies\AutorizacionPolicy;
use App\Policies\CitaPolicy;
use App\Policies\ComprobantePolicy;
use App\Policies\EstimacionOrdenPolicy;
use App\Policies\OrdenTrabajoPolicy;
use App\Policies\PagoPolicy;
use App\Policies\SubservicioPolicy;
use App\Policies\VehiculoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(OrdenTrabajo::class, OrdenTrabajoPolicy::class);
        Gate::policy(Vehiculo::class, VehiculoPolicy::class);
        Gate::policy(Cita::class, CitaPolicy::class);
        Gate::policy(Pago::class, PagoPolicy::class);
        Gate::policy(Comprobante::class, ComprobantePolicy::class);
        Gate::policy(Autorizacion::class, AutorizacionPolicy::class);
        Gate::policy(Subservicio::class, SubservicioPolicy::class);
        Gate::policy(EstimacionOrden::class, EstimacionOrdenPolicy::class);
    }
}
