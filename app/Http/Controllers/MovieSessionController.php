<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\MovieSession;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MovieSessionController extends Controller {
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

        if (!$this->seatBelongsToSessionRoom($session, $seat)) {
            return back()->withErrors(['session' => 'La butaca seleccionada no pertenece a la sala de esta sesión.']);
        }

        $ticket = $this->findTicketForSeatInSession($session, $seat);

        if (! $ticket) {
            return back()->withErrors(['session' => 'Esta butaca ya está libre.']);
        }

        DB::transaction(function () use ($ticket) {
            $purchase = $ticket->purchase;

            $ticket->delete();

            if (!$purchase) {
                return;
            }

            $this->cancelPurchaseIfWithoutTickets($purchase->id);
        });

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

    private function seatBelongsToSessionRoom(MovieSession $session, Seat $seat): bool {
        return (int) $seat->room_id === (int) $session->room_id;
    }

    private function findTicketForSeatInSession(MovieSession $session, Seat $seat): ?Ticket {
        return Ticket::query()
            ->with('purchase.payment')
            ->where('movie_session_id', $session->id)
            ->where('seat_id', $seat->id)
            ->first();
    }

    private function cancelPurchaseIfWithoutTickets(int $purchaseId): void {
        $hasTickets = Ticket::query()
            ->where('purchase_id', $purchaseId)
            ->exists();

        if ($hasTickets) {
            return;
        }

        $purchase = \App\Models\Purchase::query()->with('payment')->find($purchaseId);
        if (! $purchase) {
            return;
        }

        $purchase->update(['status' => 'cancelado']);
        $purchase->payment()?->update([
            'status' => 'fallido',
            'gateway_status' => 'liberado_por_admin',
        ]);
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