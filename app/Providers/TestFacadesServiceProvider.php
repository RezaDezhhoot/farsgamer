<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Cart\Cart;

class TestFacadesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('cart',function(){

            return new Cart();

        });

    }
}
