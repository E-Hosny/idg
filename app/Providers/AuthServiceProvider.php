<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->role === 'receptionist' || $user->role === 'admin') {
                return true;
            }
        });
        \Illuminate\Support\Facades\Gate::define('isReceptionist', function ($user) {
            return $user->role === 'receptionist' || $user->role === 'admin';
        });
    }
} 