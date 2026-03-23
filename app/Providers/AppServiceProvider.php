<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// use Illuminate\Support\Facades\DB;

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
        // DB::listen(function ($query) {
        //     info('SQL: '.$query->sql);
        //     info('Bindings: '.implode(', ', $query->bindings));
        //     info('Time: '.$query->time.'ms');
        // });
    }
}
