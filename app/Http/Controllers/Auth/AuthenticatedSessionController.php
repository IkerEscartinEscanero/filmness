<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {
        // If the user comes from a page within the site, we save the URL so that after login they return to where they were
        $reference = $request->headers->get('referer');
        if ($reference && str_starts_with($reference, config('app.url'))) {
            session()->put('url.intended', $reference);
        }

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Si es admin, siempre al panel, sin importar de dónde venía.
        if ($request->user()->role === 'admin') {
            return redirect()
                ->route('admin.dashboard')
                ->with('auth_notice', 'Bienvenido al panel administrativo, '.$request->user()->name.'.');
        }

        return redirect()
            ->intended(route('home', absolute: false))
            ->with('auth_notice', 'Has iniciado sesión. Bienvenido de nuevo, '.$request->user()->name.'.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}