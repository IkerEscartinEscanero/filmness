<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\MovieSession;
use App\Models\Purchase;
use App\Models\Review;
use App\Models\Ticket;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response {
        $user = $request->user();

        $watchedTicketsQuery = Ticket::query()
            ->where('validated', true)
            ->whereHas('purchase', fn ($query) => $query
                ->where('user_id', $user->id)
                ->where('status', 'pagado'))
            ->whereHas('movieSession', fn ($query) => $query->where('date', '<', now()))
            ->with([
                'movieSession:id,film_id,date',
                'movieSession.film:id,title,poster,logo',
            ]);

        $watchedFilms = (clone $watchedTicketsQuery)
            ->get()
            ->map(function (Ticket $ticket) {
                $film = $ticket->movieSession?->film;
                $sessionDate = $ticket->movieSession?->date;

                if (! $film || ! $sessionDate) {
                    return null;
                }

                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'poster' => $film->poster,
                    'logo' => $film->logo,
                    'watchedAt' => $sessionDate->format('d/m/Y H:i'),
                ];
            })
            ->filter()
            ->unique('id')
            ->values();

        $reviews = Review::query()
            ->where('user_id', $user->id)
            ->whereHas('film.movieSessions.tickets', fn ($query) => $query
                ->where('validated', true)
                ->whereHas('purchase', fn ($purchaseQuery) => $purchaseQuery
                    ->where('user_id', $user->id)
                    ->where('status', 'pagado')))
            ->with('film:id,title,logo')
            ->orderByDesc('date')
            ->get()
            ->map(fn (Review $review) => [
                'id' => $review->id,
                'filmTitle' => $review->film?->title,
                'filmLogo' => $review->film?->logo,
                'date' => optional($review->date)->format('d/m/Y') ?: $review->created_at->format('d/m/Y'),
                'stars' => (int) $review->stars,
                'comment' => $review->comment,
            ]);

        $paidPurchases = Purchase::query()
            ->where('user_id', $user->id)
            ->where('status', 'pagado');

        $nextSession = MovieSession::query()
            ->where('date', '>=', now())
            ->whereHas('tickets.purchase', function ($query) use ($user) {
                $query
                    ->where('user_id', $user->id)
                    ->where('status', 'pagado');
            })
            ->with('film:id,title')
            ->orderBy('date')
            ->first();

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'birth_date' => optional($user->birth_date)->format('Y-m-d'),
                'avatar_url' => $this->avatarUrl($user->avatar_path),
            ],
            'watchedFilms' => $watchedFilms,
            'reviews' => $reviews,
            'stats' => [
                'tickets' => (clone $watchedTicketsQuery)->count(),
                'purchases' => (clone $paidPurchases)->count(),
                'spent' => (float) (clone $paidPurchases)->sum('total'),
            ],
            'nextSession' => $nextSession
                ? [
                    'film' => $nextSession->film?->title,
                    'date' => $nextSession->date->format('d/m/Y H:i'),
                ]
                : null,
            'discounts' => Discount::query()
                ->where('user_id', $user->id)
                ->orderByDesc('active')
                ->orderBy('expiration_date')
                ->get()
                ->map(fn (Discount $discount) => [
                    'id' => $discount->id,
                    'label' => $discount->type === 'porcentaje' ? 'Descuento porcentual' : 'Descuento fijo',
                    'value' => $discount->type === 'porcentaje'
                        ? rtrim(rtrim((string) $discount->value, '0'), '.').'%'
                        : number_format((float) $discount->value, 2).' EUR',
                    'active' => $discount->active,
                    'expiration_date' => optional($discount->expiration_date)->format('d/m/Y'),
                ]),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse {
        $user = $request->user();

        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'birth_date' => $validated['birth_date'] ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $user->avatar_path = $request->file('avatar')->store('profiles', 'public');
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function avatarUrl(?string $avatarPath): string {
        if (! $avatarPath) {
            return '/images/Render3.png';
        }

        return '/storage/'.$avatarPath;
    }
}