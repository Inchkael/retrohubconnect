<?php
namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\ServiceCategory;
use App\Services\GeocodingService;
use App\Observers\UserObserver;

/**
 * AppServiceProvider - Fournisseur de services principal de l'application
 *
 * Ce provider est responsable de :
 * 1. L'enregistrement des services dans le conteneur IoC
 * 2. Le démarrage (boot) des fonctionnalités globales de l'application
 * 3. L'enregistrement des observers
 * 4. Le partage de données entre les vues
 */

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GeocodingService::class, function ($app) {
            return new GeocodingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrement de l'observer pour le modèle User

        User::observe(UserObserver::class);


        // Partage des catégories de services avec toutes les vues qui utilisent le layout
        View::composer('layouts.layout', function ($view) {
            $categories = ServiceCategory::where('is_validated', true)
                ->orderBy('name')
                ->get();
            $view->with('categories', $categories);
        });

        // Configuration des variables par défaut pour les vues
        View::share([
            'defaultMapCenter' => [50.640281, 4.666745], // Centre de la Belgique
            'defaultMapZoom' => 15,
        ]);
    }
}
