<?php

namespace App\Providers;

use App\Http\Context\Auth\AuthContext;
use App\Http\Context\Auth\AuthContextInterface;
use Illuminate\Support\ServiceProvider;

class ContextProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthContextInterface::class, AuthContext::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
