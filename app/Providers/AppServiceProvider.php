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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Rate limit para el panel admin: 200 requests por minuto por usuario
        RateLimiter::for('admin', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(200)->by($request->user()->id)
                : Limit::perMinute(60)->by($request->ip());
        });

        // Rate limit para el portal del cliente: 100 requests por minuto
        RateLimiter::for('cliente', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(100)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        // Rate limit para el portal del mecánico: 150 requests por minuto
        RateLimiter::for('mecanico', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(150)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        // Rate limit para el registro de usuarios: 3 registros por hora por IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });
    }
}
