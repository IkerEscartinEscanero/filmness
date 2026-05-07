<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\MovieSession;
use App\Models\Seat;
use App\Services\SeatsService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MovieSessionController extends Controller {
    public function __construct(private SeatsService $seatsService) {

    }

    public function store(Request $request, Film $film) {
        $this->ensureAdmin();

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'price' => 'required|numeric|min:0',
        ]);

        $startAt = Carbon::parse($validated['date']);

        // Reject any session that starts in the past
        if ($startAt->lt(now())) {
            return back()
                ->withErrors(['session' => 'No se pueden añadir horarios con fecha u hora pasadas.'])
                ->withInput();
        }

        // A film without duration would break overlap checks, so we enforce a minimum of 1 minute
        $newDurationMinutes = max(1, (int) ($film->duration ?? 0));
        $endAt = $startAt->copy()->addMinutes($newDurationMinutes);

        if ($this->alreadyExistsAtSameTime((int) $validated['room_id'], $startAt)) {
            return back()
                ->withErrors(['session' => 'No se puede crear el horario: ya existe una sesión en esta sala a esa misma hora.'])
                ->withInput();
        }

        $conflict = $this->findOverlapInRoom((int) $validated['room_id'], $startAt, $endAt);
        if ($conflict) {
            return back()
                ->withErrors([
                    'session' => 'No se puede crear el horario: se solapa con otra sesión de esta sala ('
                        .$conflict['start']->format('d/m H:i')
                        .' - '
                        .$conflict['end']->format('H:i')
                        .').',
                ])
                ->withInput();
        }

        try {
            MovieSession::create([
                'film_id' => $film->id,
                'room_id' => $validated['room_id'],
                'date' => $validated['date'],
                'price' => $validated['price'],
            ]);
        } catch (QueryException $e) {
            // Final DB-level safety in case of race conditions creating the same room/date
            if ((string) $e->getCode() === '23000') {
                return back()
                    ->withErrors(['session' => 'No se puede crear el horario: ya existe una sesión en esta sala a esa misma hora.'])
                    ->withInput();
            }

            throw $e;
        }

        return back()->with('success', 'Horario creado correctamente.');
    }

    public function destroy(MovieSession $session) {
        $this->ensureAdmin();

        $session->delete();

        return back()->with('success', 'Horario eliminado correctamente.');
    }

    public function releaseSeat(MovieSession $session, Seat $seat) {
        $this->ensureAdmin();

        $error = $this->seatsService->releaseSeat($session, $seat);

        if ($error) {
            return back()->withErrors(['session' => $error]);
        }

        return back()->with('success', 'Butaca liberada correctamente.');
    }

    /**
     * Shows the seat-selection view for a specific movie session
    */
    public function seats(MovieSession $session, Request $request) {
        $session->load(['film', 'room.seats']);

        $occupiedSeatIds = $session->tickets()->pluck('seat_id')->toArray();

        $seats = $session->room->seats->map(fn ($seat) => [
            'id' => $seat->id,
            'row' => $seat->row,
            'number' => $seat->number,
            'occupied' => in_array($seat->id, $occupiedSeatIds),
        ]);

        // IDs of seats that come back from the checkout if the user clicked "Cambiar butacas"
        $availableSeatIds = $seats->where('occupied', false)->pluck('id');
        $initialSeatIds = collect($request->input('seat_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $availableSeatIds->contains($id))
            ->values()
            ->all();

        return Inertia::render('Films/Public/Seats', [
            'session' => [
                'id' => $session->id,
                'date' => $session->date->toISOString(),
                'price' => (float) $session->price,
                'room' => $session->room->name,
            ],
            'film' => $session->film,
            'seats' => $seats,
            'initialSeatIds' => $initialSeatIds,
        ]);
    }

    private function ensureAdmin(): void {
        abort_unless(Auth::user()?->role === 'admin', 403);
    }

    /**
     * Checks exact duplicates: same room and same start time
    */
    private function alreadyExistsAtSameTime(int $roomId, Carbon $startAt): bool {
        return MovieSession::query()
            ->where('room_id', $roomId)
            ->where('date', $startAt)
            ->exists();
    }

    /**
     * Finds the first session in the room that overlaps the given time range
    */
    private function findOverlapInRoom(int $roomId, Carbon $newStart, Carbon $newEnd): ?array {
        $sessionsInRoom = MovieSession::query()
            ->where('room_id', $roomId)
            ->with('film:id,duration')
            ->get();

        foreach ($sessionsInRoom as $existingSession) {
            $existingStart = Carbon::parse($existingSession->date);
            $existingDurationMinutes = max(1, (int) ($existingSession->film?->duration ?? 0));
            $existingEnd = $existingStart->copy()->addMinutes($existingDurationMinutes);

            if ($newStart->lt($existingEnd) && $newEnd->gt($existingStart)) {
                return [
                    'start' => $existingStart,
                    'end' => $existingEnd,
                ];
            }
        }

        return null;
    }
}