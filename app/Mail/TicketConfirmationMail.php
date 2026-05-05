<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketConfirmationMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(public readonly Purchase $purchase) {

    }

    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Tus entradas para ' . $this->purchase->tickets->first()?->movieSession?->film?->title,
        );
    }

    public function content(): Content {
        $session = $this->purchase->tickets->first()?->movieSession;
        $film    = $session?->film;

        $seats = $this->purchase->tickets
            ->sortBy(fn ($t) => [$t->seat->row, $t->seat->number])
            ->map(fn ($t) => $t->seat->row . $t->seat->number)
            ->values();

        // Build a single QR encoding the purchase id and all ticket codes
        $allCodes = $this->purchase->tickets->pluck('qr_code')->join('|');
        $qrData = 'filmness:purchase=' . $this->purchase->id . '|tickets=' . $allCodes;
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($qrData);

        $googleCalendarUrl = $this->buildGoogleCalendarUrl($film, $session);

        return new Content(
            view: 'emails.ticket-confirmation',
            with: [
                'purchase' => $this->purchase,
                'seats' => $seats,
                'qrUrl' => $qrUrl,
                'film' => $film,
                'session' => $session,
                'googleCalendarUrl' => $googleCalendarUrl,
            ],
        );
    }

    // Build a prefilled Google Calendar "add event" URL
    private function buildGoogleCalendarUrl(?object $film, ?object $session): ?string {
        if (! $film || ! $session) {
            return null;
        }

        // Google Calendar expects dates in UTC
        $start = $session->date->utc()->format('Ymd\THis\Z');
        $end   = $session->date->copy()->addHours(2)->utc()->format('Ymd\THis\Z');

        return 'https://calendar.google.com/calendar/render?' . http_build_query([
            'action' => 'TEMPLATE',
            'text' => 'Cine: ' . $film->title,
            'dates' => $start . '/' . $end,
            'location' => 'Filmness Cinema',
        ]);
    }
}