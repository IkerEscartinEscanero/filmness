<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\MovieSessionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public profile routes for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Review routes
    Route::post('/films/{film}/reviews', [ReviewController::class, 'store'])->name('films.reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Public browsing routes
Route::get('/', [FilmController::class, 'index'])->name('home');
Route::get('/cartelera', [FilmController::class, 'billboard'])->name('billboard');
Route::get('/proximos-estrenos', [FilmController::class, 'upcoming'])->name('upcoming');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/sessions/{session}/seats', [MovieSessionController::class, 'seats'])->name('sessions.seats');
Route::get('/sobre-nosotros', fn () => Inertia::render('About'))->name('about');
Route::get('/aviso-legal', fn () => Inertia::render('Legal/LegalNotice'))->name('legal.notice');
Route::get('/condiciones-de-compra', fn () => Inertia::render('Legal/PurchaseTerms'))->name('legal.terms');
Route::get('/accesibilidad', fn () => Inertia::render('Legal/Accessibility'))->name('legal.accessibility');
Route::get('/politica-de-privacidad', fn () => Inertia::render('Legal/PrivacyPolicy'))->name('legal.privacy');
Route::get('/politica-de-cookies', fn () => Inertia::render('Legal/CookiesPolicy'))->name('legal.cookies');
Route::get('/modelodesistimiento/', function () {
    $pdfPath = public_path('legal/modelodesistimiento.pdf');

    if (! file_exists($pdfPath)) {
        abort(404, 'El documento de desistimiento no esta disponible.');
    }

    return response()->file($pdfPath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="modelo-desistimiento.pdf"',
    ]);
})->name('legal.withdrawal');

// Checkout and payment routes
Route::get('/sessions/{session}/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/sessions/{session}/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('stripe.webhook');

// Admin's management routes
Route::middleware('auth')->group(function () {
    Route::get('/films/create', [FilmController::class, 'create'])->name('films.create');
    Route::post('/films', [FilmController::class, 'store'])->name('films.store');
    Route::get('/films/{film}/edit', [FilmController::class, 'edit'])->name('films.edit');
    Route::put('/films/{film}', [FilmController::class, 'update'])->name('films.update');
    Route::delete('/films/{film}', [FilmController::class, 'destroy'])->name('films.destroy');
    Route::post('/films/{film}/sessions', [MovieSessionController::class, 'store'])->name('films.sessions.store');
    Route::delete('/sessions/{session}', [MovieSessionController::class, 'destroy'])->name('sessions.destroy');
    Route::delete('/sessions/{session}/seats/{seat}/release', [MovieSessionController::class, 'releaseSeat'])->name('sessions.seats.release');
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

require __DIR__.'/auth.php';
