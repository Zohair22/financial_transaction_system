<?php

namespace Modules\Plaid\Providers;

use Modules\Plaid\Interfaces\IClientPlaid;
use Modules\Plaid\Interfaces\IPlaidServices;
use Modules\Plaid\Services\ClientPlaid;
use Modules\Plaid\Services\PlaidServices;
use Nwidart\Modules\Support\ModuleServiceProvider;

// use Illuminate\Console\Scheduling\Schedule;

class PlaidServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Plaid';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'plaid';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(
            IClientPlaid::class,
            ClientPlaid::class
        );

        $this->app->bind(
            IPlaidServices::class,
            PlaidServices::class
        );
    }

    /**
     * Define module schedules.
     *
     * @param  $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
