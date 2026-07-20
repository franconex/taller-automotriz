<?php

namespace App\Providers;

use App\Models\Rol;
use App\Models\sucursal;
use App\Models\User;
use App\Policies\RolPolicy;
use App\Policies\SucursalPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $policies = [
        User::class => UserPolicy::class,
        Rol::class => RolPolicy::class,
        sucursal::class => SucursalPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin', fn (User $user) => $user->rol?->nombre === 'Administrador');

        Gate::before(function (User $user, string $ability) {
            if ($user->tieneRol('Administrador')) {
                return true;
            }

            if (str_contains($ability, '.')) {
                return $user->tienePermiso($ability) ?: null;
            }

            return null;
        });
    }

    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
