<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ScheduleController;

// Routes d'authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Connexion automatique (pour un accès facile)
Route::get('/auto-login', [AuthController::class, 'autoLogin'])->name('auto.login');

// Route pour créer un utilisateur administrateur (à exécuter une seule fois)
Route::get('/setup-admin', [AuthController::class, 'setupAdmin'])->name('setup.admin');

// Route d'accueil - accessible sans authentification
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Routes protégées par le middleware admin
Route::middleware('admin')->group(function () {
    // Route du tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes pour les employés
    Route::resource('employees', EmployeeController::class);

    // Routes pour l'historique des employés
    Route::get('/employees/archive', [EmployeeController::class, 'archive'])->name('employees.archive');
    Route::put('/employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
    Route::delete('/employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])->name('employees.force_delete');

    // Routes pour les plannings
    Route::resource('schedules', ScheduleController::class);

    // Routes pour l'historique des plannings
    Route::get('/schedules/archive', [ScheduleController::class, 'archive'])->name('schedules.archive');
    Route::put('/schedules/{id}/restore', [ScheduleController::class, 'restore'])->name('schedules.restore');
    Route::delete('/schedules/{id}/force-delete', [ScheduleController::class, 'forceDelete'])->name('schedules.force_delete');

    // Routes pour les rapports
    Route::get('/reports/monthly', [DashboardController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/annual', [DashboardController::class, 'annualReport'])->name('reports.annual');

    // Route pour le diagramme horaire par restaurant
    Route::get('/reports/timeline/{store_id?}', [DashboardController::class, 'timelineReport'])->name('reports.timeline');
});
