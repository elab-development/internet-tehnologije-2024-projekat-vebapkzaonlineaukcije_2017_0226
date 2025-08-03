<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home'; // Može biti '/' ili neka druga ruta po tvojoj želji

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot(): void
    {
        // Rate Limiting za API rute
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // GLAVNI DEO: Mapiranje i učitavanje rutnih fajlova
        // Ovaj kod je ključan i uključuje "$this->map()" metodu ako tvoja verzija Laravela to zahteva.
        // U modernim Laravel verzijama, $this->routes() metoda je deo parent ServiceProvidera.
        // Ali ako tvoja verzija nema tu metodu, onda je ovaj pristup mapiranja ispravniji.
        $this->mapApiRoutes();
        $this->mapWebRoutes();

        // Opcionalno: Možeš dodati i druge rute ako ih imaš, npr. mapConsoleRoutes(), mapChannelRoutes()
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
             ->middleware('api')
             ->group(base_path('routes/api.php'));
    }
}