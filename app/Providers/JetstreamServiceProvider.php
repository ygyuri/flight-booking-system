<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use App\Providers\AppServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;


class JetstreamServiceProvider extends ServiceProvider
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
        $this->configurePermissions();

        Fortify::authenticateUsing(function (Request $request) {
            if ($user = Auth::guard('admin')->attempt($request->only('email', 'password'))) {
                return $user;
            }

            if ($user = Auth::guard('customer')->attempt($request->only('email', 'password'))) {
                return $user;
            }
        });
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}