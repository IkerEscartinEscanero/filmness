<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\MovieSessionController;
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
Route::get('/sessions/{session}/seats', [MovieSessionController::class, 'seats'])->name('sessions.seats');
Route::get('/sessions/{session}/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/sessions/{session}/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('stripe.webhook');
Route::get('/sobre-nosotros', fn () => Inertia::render('About'))->name('about');

// Movie management routes (admins only)
Route::middleware('auth')->group(function () {
    Route::get('/films/create', [FilmController::class, 'create'])->name('films.create');
    Route::post('/films', [FilmController::class, 'store'])->name('films.store');
    Route::get('/films/{film}/edit', [FilmController::class, 'edit'])->name('films.edit');
    Route::put('/films/{film}', [FilmController::class, 'update'])->name('films.update');
    Route::delete('/films/{film}', [FilmController::class, 'destroy'])->name('films.destroy');
    Route::post('/films/{film}/sessions', [MovieSessionController::class, 'store'])->name('films.sessions.store');
    Route::delete('/sessions/{session}', [MovieSessionController::class, 'destroy'])->name('sessions.destroy');
});

require __DIR__.'/auth.php';
