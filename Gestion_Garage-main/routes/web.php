<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TechnicienController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VehiculeOccasionController;

/*
|--------------------------------------------------------------------------
| Web Routes - Garage Mécano D-CLIC
|--------------------------------------------------------------------------
*/

// Accueil & Authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'postLogin'])->middleware('throttle:5,1')->name('login.post');
    Route::post('/post-login', [LoginController::class, 'postLogin'])->middleware('throttle:5,1');
    Route::get('/registration', [LoginController::class, 'inscription'])->name('register');
    Route::get('/register', [LoginController::class, 'inscription']);
    Route::post('/registration', [LoginController::class, 'postRegistration'])->middleware('throttle:5,1')->name('register.post');
    Route::post('/register', [LoginController::class, 'postRegistration'])->middleware('throttle:5,1');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/', [LoginController::class, 'show'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/vehicules-occasion', [VehiculeOccasionController::class, 'index'])->name('vehicules-occasion.index');
});

// ==========================================
// Espace Administrateur
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/home', [AdminController::class, 'dashboard']);

    // Gestion des Véhicules
    Route::get('/vehicules', [AdminController::class, 'vehicules'])->name('admin.vehicules');
    Route::post('/vehicules', [AdminController::class, 'storeVehicule'])->name('admin.vehicules.store');
    Route::get('/vehicules/{id}', [AdminController::class, 'showVehicule'])->name('admin.vehicules.show');
    Route::put('/vehicules/{id}', [AdminController::class, 'updateVehicule'])->name('admin.vehicules.update');
    Route::delete('/vehicules/{id}', [AdminController::class, 'destroyVehicule'])->name('admin.vehicules.destroy');

    // Gestion des Réparations
    Route::get('/reparations', [AdminController::class, 'reparations'])->name('admin.reparations');
    Route::post('/reparations', [AdminController::class, 'storeReparation'])->name('admin.reparations.store');
    Route::put('/reparations/{id}/status', [AdminController::class, 'updateReparationStatus'])->name('admin.reparations.updateStatus');
    Route::post('/reparations/{id}/notifier', [AdminController::class, 'notifierClient'])->name('admin.reparations.notifier');
    Route::get('/preview-mail/{id}', [AdminController::class, 'previewMail'])->name('admin.previewMail');
    Route::get('/vehicules-occasion', [VehiculeOccasionController::class, 'index'])->name('admin.vehicules-occasion');
    Route::put('/vehicules-occasion/{vehiculeOccasion}/prix', [VehiculeOccasionController::class, 'updatePrice'])
        ->name('admin.vehicules-occasion.price');

    // Gestion des Techniciens
    Route::get('/techniciens', [AdminController::class, 'techniciens'])->name('admin.techniciens');
    Route::post('/techniciens', [AdminController::class, 'storeTechnicien'])->name('admin.techniciens.store');

    // Gestion des Clients
    Route::get('/clients', [AdminController::class, 'clients'])->name('admin.clients');
    Route::get('/users', [AdminController::class, 'clients'])->name('admin.users'); // Compatibilité

    // Pièces & Facturation
    Route::resource('pieces', PieceController::class);
    Route::get('/liste-pieces', [PieceController::class, 'index'])->name('listepiece');
    Route::resource('factures', FactureController::class);
});

// ==========================================
// Espace Technicien
// ==========================================
Route::middleware(['auth', 'role:technicien,mecanicien'])->prefix('technicien')->group(function () {
    Route::get('/dashboard', [TechnicienController::class, 'dashboard'])->name('technicien.dashboard');
    Route::put('/reparations/{id}/status', [TechnicienController::class, 'updateStatus'])->name('technicien.reparations.updateStatus');
    Route::post('/reparations/{id}/notifier', [TechnicienController::class, 'notifyClient'])->middleware('throttle:3,1')->name('technicien.reparations.notifier');
});
// Redirection de compatibilité pour l'ancien chemin /mechanic
Route::middleware(['auth', 'role:technicien,mecanicien'])->prefix('mechanic')->group(function () {
    Route::get('/home', [TechnicienController::class, 'dashboard'])->name('mec.dashboard');
});

// ==========================================
// Espace Client
// ==========================================
Route::middleware(['auth', 'role:client'])->prefix('client')->group(function () {
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/home', [ClientPortalController::class, 'dashboard']);
});
