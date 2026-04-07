<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FilmController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
// use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [FilmController::class, 'index'])->name('home');

// TODO: ruta para el perfil del usuario, con sus datos, películas vistas, reseñas, descuentos...
// Route::get('/perfil', function () {
//     return Inertia::render('Profile', [
//         'user' => auth()->user(),
//         'movies' => [], // luego
//         'reviews' => [],
//         'discounts' => []
//     ]);
// })->middleware(['auth', 'verified'])->name('perfil o profile');

require __DIR__.'/auth.php';
