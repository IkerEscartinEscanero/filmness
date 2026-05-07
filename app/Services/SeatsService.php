<?php

namespace App\Services;

use App\Models\MovieSession;
use App\Models\Purchase;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class SeatsService {
    /**
     * Validates and releases a seat from a session.
     * Returns null on success or an error message string on failure.
     */
    public function releaseSeat(MovieSession $session, Seat $seat): ?string {
        if (! $this->seatBelongsToSessionRoom($session, $seat)) {
            return 'La butaca seleccionada no pertenece a la sala de esta sesión.';
        }

        $ticket = $this->findTicketForSeatInSession($session, $seat);

        if (! $ticket) {
            return 'Esta butaca ya está libre.';
        }

        DB::transaction(function () use ($ticket) {
            $purchase = $ticket->purchase;

            $ticket->delete();

            if (! $purchase) {
                return;
            }

            $this->cancelPurchaseIfWithoutTickets($purchase->id);
        });

        return null;
    }

    public function seatBelongsToSessionRoom(MovieSession $session, Seat $seat): bool {
        return (int) $seat->room_id === (int) $session->room_id;
    }

    public function findTicketForSeatInSession(MovieSession $session, Seat $seat): ?Ticket {
        return Ticket::query()
            ->with('purchase.payment')
            ->where('movie_session_id', $session->id)
            ->where('seat_id', $seat->id)
            ->first();
    }

    public function cancelPurchaseIfWithoutTickets(int $purchaseId): void {
        $hasTickets = Ticket::query()
            ->where('purchase_id', $purchaseId)
            ->exists();

        if ($hasTickets) {
            return;
        }

        $purchase = Purchase::query()->with('payment')->find($purchaseId);
        if (! $purchase) {
            return;
        }

        $purchase->update(['status' => 'cancelado']);
        $purchase->payment()?->update([
            'status' => 'fallido',
            'gateway_status' => 'liberado_por_admin',
        ]);
    }
}