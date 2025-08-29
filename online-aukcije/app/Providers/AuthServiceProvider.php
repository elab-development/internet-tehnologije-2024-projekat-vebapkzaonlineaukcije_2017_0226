<?php

namespace App\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider; 
use Illuminate\Support\Facades\Gate;
use App\Models\Aukcija;
use App\Policies\AukcijaPolicy;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Aukcija::class => AukcijaPolicy::class,
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
