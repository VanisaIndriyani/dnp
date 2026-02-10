<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::define('super_admin', function (User $user) {
            return $user->role === 'super_admin';
        });

        Gate::define('admin', function (User $user) {
            return $user->role === 'admin' || $user->role === 'super_admin';
        });

        Gate::define('operator', function (User $user) {
            return $user->role === 'operator';
        });
    }
}
