<?php

namespace App\Mail;

use App\Models\Purchase;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
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
        $tickets = $this->purchase->tickets->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'seat' => $ticket->seat->row . $ticket->seat->number,
                'qr_svg' => $this->generateQrSvg($ticket->qr_code),
                'qr_code' => $ticket->qr_code,
            ];
        });

        $session = $this->purchase->tickets->first()?->movieSession;
        $film    = $session?->film;

        $googleCalendarUrl = $this->buildGoogleCalendarUrl($film, $session);

        return new Content(
            view: 'emails.ticket-confirmation',
            with: [
                'purchase' => $this->purchase,
                'tickets' => $tickets,
                'film' => $film,
                'session' => $session,
                'googleCalendarUrl' => $googleCalendarUrl,
            ],
        );
    }

    // Generate an inline SVG string for the given QR code text
    private function generateQrSvg(string $text): string {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd(),
        );

        $writer = new Writer($renderer);

        return $writer->writeString($text);
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
            'action'   => 'TEMPLATE',
            'text'     => 'Cine: ' . $film->title,
            'dates'    => $start . '/' . $end,
            'details'  => 'Entrada comprada en Filmness. Pedido #' . $this->purchase->id,
            'location' => 'Filmness Cinema',
        ]);
    }
}