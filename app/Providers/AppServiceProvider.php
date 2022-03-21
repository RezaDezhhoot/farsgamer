<?php

namespace App\Providers;

use App\Repositories\Classes\ArticleRepository;
use App\Repositories\Classes\CategoryRepository;
use App\Repositories\Classes\ChatRepository;
use App\Repositories\Classes\OffendRepository;
use App\Repositories\Classes\OrderRepository;
use App\Repositories\Classes\OrderTransactionRepository;
use App\Repositories\Classes\PlatformRepository;
use App\Repositories\Classes\SettingRepository;
use App\Repositories\Classes\UserRepository;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\OffendRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use App\Repositories\Interfaces\PlatformRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class,
        );

        $this->app->bind(
            PlatformRepositoryInterface::class,
            PlatformRepository::class,
        );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class,
        );

        $this->app->bind(
            OrderTransactionRepositoryInterface::class,
            OrderTransactionRepository::class,
        );

        $this->app->bind(
            SettingRepositoryInterface::class,
            SettingRepository::class,
        );

        $this->app->bind(
            ChatRepositoryInterface::class,
            ChatRepository::class,
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class,
        );

        $this->app->bind(
            OffendRepositoryInterface::class,
            OffendRepository::class,
        );

        $this->app->bind(
            ArticleRepositoryInterface::class,
            ArticleRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
