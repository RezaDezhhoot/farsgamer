<?php

namespace App\Providers;

use App\Repositories\Classes\ArticleCategoryRepository;
use App\Repositories\Classes\ArticleRepository;
use App\Repositories\Classes\CardRepository;
use App\Repositories\Classes\CategoryRepository;
use App\Repositories\Classes\ChatRepository;
use App\Repositories\Classes\CommentRepository;
use App\Repositories\Classes\NotificationRepository;
use App\Repositories\Classes\OffendRepository;
use App\Repositories\Classes\OrderRepository;
use App\Repositories\Classes\OrderTransactionRepository;
use App\Repositories\Classes\ParameterRepository;
use App\Repositories\Classes\PaymentRepository;
use App\Repositories\Classes\PlatformRepository;
use App\Repositories\Classes\RequestRepository;
use App\Repositories\Classes\SendRepository;
use App\Repositories\Classes\SettingRepository;
use App\Repositories\Classes\UserRepository;
use App\Repositories\Interfaces\ArticleCategoryRepositoryInterface;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\OffendRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\OrderTransactionRepositoryInterface;
use App\Repositories\Interfaces\ParameterRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\PlatformRepositoryInterface;
use App\Repositories\Interfaces\RequestRepositoryInterface;
use App\Repositories\Interfaces\SendRepositoryInterface;
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

        $this->app->bind(
            ArticleCategoryRepositoryInterface::class,
            ArticleCategoryRepository::class,
        );

        $this->app->bind(
            CardRepositoryInterface::class,
            CardRepository::class,
        );

        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class,
        );

        $this->app->bind(
            SendRepositoryInterface::class,
            SendRepository::class,
        );

        $this->app->bind(
            ParameterRepositoryInterface::class,
            ParameterRepository::class,
        );

        $this->app->bind(
            CommentRepositoryInterface::class,
            CommentRepository::class,
        );

        $this->app->bind(
            PaymentRepositoryInterface::class,
            PaymentRepository::class,
        );

        $this->app->bind(
            RequestRepositoryInterface::class,
            RequestRepository::class,
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
