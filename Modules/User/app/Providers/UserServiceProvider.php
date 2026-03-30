<?php

namespace Modules\User\Providers;

use Modules\User\Interfaces\IAuthService;
use Modules\User\Interfaces\IUserRepository;
use Modules\User\Repositories\UserRepository;
use Modules\User\Services\AuthService;
use Nwidart\Modules\Support\ModuleServiceProvider;

class UserServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'User';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'user';

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

        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IAuthService::class, AuthService::class);
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
