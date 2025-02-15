<?php

namespace App\Providers;
use App\Services\GreetingService;
use Illuminate\Support\ServiceProvider;

class practice extends ServiceProvider
{
    public function register(): void
    {
        // $this->app is servie container
      $this->app->bind('container',function($app){
        return new GreetingService(); 

      });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
