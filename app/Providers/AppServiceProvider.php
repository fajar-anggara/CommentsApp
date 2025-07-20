<?php

namespace App\Providers;

use App\Http\Helpers\ExactImplementers\FractalHelperImpl;
use App\Http\Helpers\ExactImplementers\LogHelperImpl;
use App\Repositories\DatabaseImplementers\AuthenticationImpl;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
//        $this->app->bind(AuthenticationRepository::class, AuthenticationImpl::class);
//        $this->app->bind(LogHelper::class, LogHelperImpl::class);
//        $this->app->bind(FractalHelper::class, FractalHelperImpl::class);

        $this->app->bind('Fractal', function ($app) {
            return new FractalHelperImpl();
        });
        $this->app->bind('SetLog', function ($app) {
            return new LogHelperImpl();
        });
        $this->app->bind('AuthDo', function ($app) {
           return new AuthenticationImpl();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
