<?php

namespace App\Providers;

use App\Helpers\ExactImplementers\FractalHelperImpl;
use App\Helpers\ExactImplementers\LogHelperImpl;
use App\Repositories\DatabaseImplementers\ArticleImpl;
use App\Repositories\DatabaseImplementers\CommenterImpl;
use App\Repositories\DatabaseImplementers\CommentImpl;
use App\Repositories\DatabaseImplementers\TenantImpl;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
//        $this->app->bind(CommenterRepository::class, CommenterImpl::class);
//        $this->app->bind(LogHelper::class, LogHelperImpl::class);
//        $this->app->bind(FractalHelper::class, FractalHelperImpl::class);

        $this->app->bind('Fractal', function ($app) {
            return new FractalHelperImpl();
        });
        $this->app->bind('SetLog', function ($app) {
            return new LogHelperImpl();
        });
        $this->app->bind('AuthDo', function ($app) {
           return new CommenterImpl();
        });
        $this->app->bind('Tenant', function ($app) {
            return new TenantImpl();
        });
        $this->app->bind('Article', function ($app) {
            return new ArticleImpl();
        });
        $this->app->bind('CommentDo', function ($app) {
            return new CommentImpl();
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
