<?php

namespace App\Providers;

use App\Repositories\DatabaseImplementers\AuthenticationImpl;
use App\Repositories\Interfaces\AuthenticationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticationRepository::class, AuthenticationImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
