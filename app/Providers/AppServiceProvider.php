<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Utiliser Bootstrap pour le style de la pagination
        Paginator::useBootstrap();
        
        // Définir la taille par défaut des chaînes de caractères
        // Evite les erreurs sur certaines bases de données MySQL
        Schema::defaultStringLength(191);
        
        // Définir la langue par défaut de l'application en français
        app()->setLocale('fr');
        
        // Charger les helpers personnalisés
        require_once app_path('Helpers/ColorHelper.php');
    }
}
