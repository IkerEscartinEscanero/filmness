<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Review;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Film $film)
    {
        $user = $request->user();

        // Validate that user has a validated ticket for this film
        $hasValidatedTicket = Ticket::query()
            ->where('validated', true)
            ->whereHas('purchase', fn ($query) => $query
                ->where('user_id', $user->id)
                ->where('status', 'pagado'))
            ->whereHas('movieSession', fn ($query) => $query
                ->where('film_id', $film->id)
                ->where('date', '<', now()))
            ->exists();

        if (!$hasValidatedTicket) {
            return Redirect::back()->with('error', 'No tienes entrada validada para esta película.');
        }

        // Check if user already has a review for this film
        $existingReview = Review::query()
            ->where('user_id', $user->id)
            ->where('film_id', $film->id)
            ->first();

        if ($existingReview) {
            return Redirect::back()->with('error', 'Ya has dejado una reseña para esta película.');
        }

        $validated = $request->validate([
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'user_id' => $user->id,
            'film_id' => $film->id,
            'stars' => $validated['stars'],
            'comment' => $validated['comment'],
            'date' => now(),
        ]);

        return Redirect::back()->with('status', 'review-created');
    }

    /**
     * Delete the specified review from storage.
     */
    public function destroy(Review $review, Request $request)
    {
        $user = $request->user();

        // Only the review owner can delete it
        if ($review->user_id !== $user->id) {
            return Redirect::back()->with('error', 'No tienes permiso para eliminar esta reseña.');
        }

        $review->delete();

        return Redirect::back()->with('status', 'review-deleted');
    }
}