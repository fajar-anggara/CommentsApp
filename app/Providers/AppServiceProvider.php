<?php

namespace App\Providers;

use App\Helpers\ArticleHelper;
use App\Helpers\AuthHelper;
use App\Helpers\CommentHelper;
use App\Helpers\FractalHelper;
use App\Helpers\Interfaces\LikeHelperInterface;
use App\Helpers\Interfaces\VotingHelperInterface;
use App\Helpers\LikeHelper;
use App\Helpers\SetLogHelper;
use App\Helpers\VotingHelper;
use App\Models\Comment;
use App\Policies\CommentPolicy;
use App\Repositories\DatabaseImplementers\ArticleImpl;
use App\Repositories\DatabaseImplementers\CommenterImpl;
use App\Repositories\DatabaseImplementers\CommentImpl;
use App\Repositories\DatabaseImplementers\ModeratingImpl;
use App\Repositories\DatabaseImplementers\ReportImpl;
use App\Repositories\DatabaseImplementers\StatisticImpl;
use App\Repositories\DatabaseImplementers\TenantImpl;
use App\Repositories\DatabaseImplementers\VoteImpl;
use App\Repositories\Interfaces\ArticleRepository;
use App\Repositories\Interfaces\CommenterRepository;
use App\Repositories\Interfaces\CommentRepository;
use App\Repositories\Interfaces\LikeRepository;
use App\Repositories\Interfaces\ModeratingRepository;
use App\Repositories\Interfaces\ReportRepository;
use App\Repositories\Interfaces\StatisticRepository;
use App\Repositories\Interfaces\TenantRepository;
use App\Repositories\Interfaces\VoteRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ArticleRepository::class, ArticleImpl::class);
        $this->app->bind(CommenterRepository::class, CommenterImpl::class);
        $this->app->bind(CommentRepository::class, CommentImpl::class);
        $this->app->bind(LikeRepository::class, LikeHelper::class);
        $this->app->bind(ModeratingRepository::class, ModeratingImpl::class);
        $this->app->bind(ReportRepository::class, ReportImpl::class);
        $this->app->bind(StatisticRepository::class, StatisticImpl::class);
        $this->app->bind(TenantRepository::class, TenantImpl::class);
        $this->app->bind(VoteRepository::class, VoteImpl::class);

        $this->app->bind('Article', function ($app) {
            return new ArticleHelper();
        });
        $this->app->bind('Auth', function ($app) {
            return new AuthHelper();
        });
        $this->app->bind('Comment', function ($app) {
            return new CommentHelper();
        });
        $this->app->bind('Fractal', function ($app) {
            return new FractalHelper();
        });
        $this->app->bind('Like', function ($app) {
            return new LikeHelper();
        });
        $this->app->bind('SetLog', function ($app) {
            return new SetLogHelper();
        });
        $this->app->bind('Tenant', function ($app) {
            return new TenantImpl();
        });
        $this->app->bind('Voting', function ($app) {
            return new VotingHelper();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Comment::class, CommentPolicy::class);
    }
}

