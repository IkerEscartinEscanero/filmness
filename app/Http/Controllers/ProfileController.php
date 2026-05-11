<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\MovieSession;
use App\Models\Purchase;
use App\Models\Review;
use App\Models\Ticket;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\DiscountService;
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
    public function edit(Request $request, DiscountService $discountService): Response {
        $user = $request->user();

        $discountService->ensureBirthdayDiscount($user);

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
            ->map(function (Ticket $ticket) use ($user) {
                $film = $ticket->movieSession?->film;
                $sessionDate = $ticket->movieSession?->date;

                if (! $film || ! $sessionDate) {
                    return null;
                }

                $userReview = Review::query()
                    ->where('user_id', $user->id)
                    ->where('film_id', $film->id)
                    ->first();

                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'poster' => $film->poster,
                    'logo' => $film->logo,
                    'watchedAt' => $sessionDate->format('d/m/Y H:i'),
                    'reviewId' => $userReview?->id,
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

        $userDiscounts = Discount::query()
            ->where('user_id', $user->id)
            ->get();

        $discountReasons = [
            Discount::REASON_WELCOME,
            Discount::REASON_BIRTHDAY,
            Discount::REASON_LARGE_PURCHASE,
        ];

        $discounts = collect($discountReasons)
            ->map(function (string $reason) use ($userDiscounts, $discountService) {
                $discountsByReason = $userDiscounts->where('reason', $reason)->values();
                $isLargePurchase = $reason === Discount::REASON_LARGE_PURCHASE;

                $availableDiscount = $discountsByReason
                    ->first(fn (Discount $discount) => $discount->active
                        && (! $discount->expiration_date || $discount->expiration_date->isFuture() || $discount->expiration_date->isToday()));

                $hasUsedOrExpired = $discountsByReason
                    ->contains(fn (Discount $discount) => ! $discount->active
                        || ($discount->expiration_date && $discount->expiration_date->isPast()));

                // If there is currently an available discount of this type, show it as not used.
                $used = $isLargePurchase
                    ? false
                    : (! $availableDiscount && $hasUsedOrExpired);

                $referenceDiscount = $availableDiscount
                    ?? $discountsByReason->sortByDesc('created_at')->first();

                $value = $referenceDiscount
                    ? ($referenceDiscount->type === 'porcentaje'
                        ? rtrim(rtrim((string) $referenceDiscount->value, '0'), '.').'%'
                        : number_format((float) $referenceDiscount->value, 2).' EUR')
                    : '10%';

                return [
                    'key' => $reason,
                    'reason' => $reason,
                    'label' => $discountService->labelFor($reason),
                    'value' => $value,
                    'available' => $isLargePurchase ? true : (bool) $availableDiscount,
                    'used' => $used,
                    'expiration_date' => optional($availableDiscount?->expiration_date)->format('d/m/Y'),
                ];
            })
            ->values();

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
            'discounts' => $discounts,
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