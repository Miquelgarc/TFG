<?php

namespace App\Providers;
use Inertia\Inertia;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        Inertia::share([
            'auth' => fn() => [
                'user' => Auth::check()
                    ? Auth::user()->load('role') // 👈 cargamos la relación
                    : null,
            ],
        ]);
    }
    /* public function share(Request $request)
    {
        return [
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    } */
}
