<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FilmController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [FilmController::class, 'index'])->name('home');
Route::get('/cartelera', [FilmController::class, 'billboard'])->name('billboard');
Route::get('/proximos-estrenos', [FilmController::class, 'upcoming'])->name('upcoming');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/sobre-nosotros', fn () => Inertia::render('About'))->name('about');

// Rutas para gestión de películas (solo admins)
Route::middleware('auth')->group(function () {
    Route::get('/films/create', [FilmController::class, 'create'])->name('films.create');
    Route::post('/films', [FilmController::class, 'store'])->name('films.store');
    Route::get('/films/{film}/edit', [FilmController::class, 'edit'])->name('films.edit');
    Route::put('/films/{film}', [FilmController::class, 'update'])->name('films.update');
    Route::delete('/films/{film}', [FilmController::class, 'destroy'])->name('films.destroy');
});

require __DIR__.'/auth.php';
