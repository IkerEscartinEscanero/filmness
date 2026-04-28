<?php

namespace App\Http\Controllers;

use App\Models\MovieSession;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Event as StripeEvent;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Webhook;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CheckoutController extends Controller {
    // Build the checkout summary page from selected seats and session info
    public function create(Request $request, MovieSession $session): Response|RedirectResponse {
        $session->load(['film', 'room.seats', 'tickets']);

        $seatIds = $this->validatedSeatIds($request, $session);
        if (count($seatIds) === 0) {
            return redirect()->route('sessions.seats', $session)->with('error', 'Debes seleccionar al menos una butaca antes de continuar.');
        }

        $selectedSeats = $session->room->seats
            ->whereIn('id', $seatIds)
            ->sortBy([
                ['row', 'asc'],
                ['number', 'asc'],
            ])
            ->values()
            ->map(fn ($seat) => [
                'id' => $seat->id,
                'row' => $seat->row,
                'number' => $seat->number,
                'label' => $seat->row.$seat->number,
            ]);

        return Inertia::render('Checkout/Create', [
            'session' => [
                'id' => $session->id,
                'date' => $session->date->toISOString(),
                'price' => (float) $session->price,
                'room' => $session->room->name,
            ],
            'film' => [
                'id' => $session->film->id,
                'title' => $session->film->title,
            ],
            'selectedSeats' => $selectedSeats,
            'total' => (float) bcmul((string) $session->price, (string) count($selectedSeats), 2),
            'contactEmail' => Auth::user()?->email,
            'stripeKeyConfigured' => filled(config('services.stripe.key')) && filled(config('services.stripe.secret')),
            'cancelled' => $request->boolean('cancelled'),
        ]);
    }

    // Validate input, create local pending records and redirect to Stripe Checkout
    public function store(Request $request, MovieSession $session): RedirectResponse {
        $session->load(['film', 'room.seats', 'tickets']);

        $validated = $request->validate([
            'email' => ['required', 'email:rfc'],
            'seat_ids' => ['required', 'array', 'min:1'],
            'seat_ids.*' => ['integer'],
        ]);

        $seatIds = $this->validatedSeatIds($request, $session);
        if (count($seatIds) === 0) {
            return redirect()->route('sessions.seats', $session)->with('error', 'Las butacas seleccionadas ya no estan disponibles.');
        }

        if (! filled(config('services.stripe.secret')) || ! filled(config('services.stripe.key'))) {
            return back()->withErrors(['payment' => 'Stripe no esta configurado todavia en el entorno.'])->withInput();
        }

        $total = (float) bcmul((string) $session->price, (string) count($seatIds), 2);

        // Persist a pending purchase before contacting Stripe, to have a local record to correlate with the Stripe session and update later in the webhook.
        $purchase = Purchase::create([
            'user_id' => Auth::id(),
            'contact_email' => $validated['email'],
            'total' => $total,
            'status' => 'pendiente',
        ]);

        // Payment starts as pending or failed until webhook confirmation arrives
        $payment = Payment::create([
            'purchase_id' => $purchase->id,
            'payment_method' => 'stripe',
            'status' => 'fallido',
            'gateway_status' => 'pendiente',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $checkoutSession = StripeCheckoutSession::create([
                'mode' => 'payment',
                'customer_email' => $validated['email'],
                'success_url' => route('checkout.success', [
                    'purchase' => $purchase->id,
                    'session_id' => '{CHECKOUT_SESSION_ID}',
                ]),
                'cancel_url' => route('checkout.cancel', [
                    'session' => $session->id,
                    'seat_ids' => $seatIds,
                    'cancelled' => 1,
                ]),
                // Stripe sends metadata back in webhook events
                'metadata' => [
                    'purchase_id' => (string) $purchase->id,
                    'movie_session_id' => (string) $session->id,
                    'seat_ids' => implode(',', $seatIds),
                    'contact_email' => $validated['email'],
                ],
                'line_items' => [[
                    'quantity' => count($seatIds),
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => (int) round(((float) $session->price) * 100),
                        'product_data' => [
                            'name' => 'Entradas para '.$session->film->title,
                            'description' => 'Sala '.$session->room->name.' · '.$session->date->format('d/m/Y H:i'),
                        ],
                    ],
                ]],
            ]);
        } catch (ApiErrorException $exception) {
            $payment->delete();
            $purchase->delete();

            return back()->withErrors(['payment' => 'No se ha podido iniciar el pago con Stripe: '.$exception->getMessage()])->withInput();
        }

        $payment->update([
            'gateway_status' => 'checkout_creado',
            'stripe_checkout_session_id' => $checkoutSession->id,
            'stripe_payment_intent_id' => is_string($checkoutSession->payment_intent) ? $checkoutSession->payment_intent : null,
        ]);

        return redirect()->away($checkoutSession->url);
    }

    // -- Return page after Stripe redirects the user back to the app --
    // The real payment confirmation is done by webhook, this is just a landing page
    public function success(Request $request): Response {
        $purchase = Purchase::query()->with('payment')->findOrFail($request->integer('purchase'));

        return Inertia::render('Checkout/Success', [
            'purchase' => [
                'id' => $purchase->id,
                'contactEmail' => $purchase->contact_email,
                'total' => (float) $purchase->total,
                'status' => $purchase->status,
            ],
            'payment' => [
                'gatewayStatus' => $purchase->payment?->gateway_status,
                'stripeSessionId' => $request->string('session_id')->toString(),
            ],
        ]);
    }

    public function cancel(Request $request, MovieSession $session): RedirectResponse {
        return redirect()->route('checkout.create', [
            'session' => $session->id,
            'seat_ids' => $request->input('seat_ids', []),
            'cancelled' => 1,
        ]);
    }

    // Stripe calls this endpoint asynchronously
    public function webhook(Request $request): HttpResponse {
        $webhookSecret = config('services.stripe.webhook_secret');

        if (! filled($webhookSecret)) {
            return response('Missing Stripe webhook secret.', 500);
        }

        $signature = $request->header('Stripe-Signature', '');
        $payload = $request->getContent();

        try {
            // Verify Stripe signature to avoid forged requests and parse the event
            $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\Throwable $e) {
            return response('Invalid webhook signature.', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $this->handleCheckoutCompleted($event);
        }

        if (in_array($event->type, ['checkout.session.expired', 'checkout.session.async_payment_failed'], true)) {
            $this->handleCheckoutFailed($event);
        }

        return response('ok', 200);
    }

    /* 
        Creates tickets atomically and marks purchase and payment as paid after receiving confirmation from 
        Stripe that the checkout session was completed successfully 
    */
    private function handleCheckoutCompleted(StripeEvent $event): void {
        /** @var \Stripe\Checkout\Session $stripeSession */
        $stripeSession = $event->data->object;

        $purchaseId = (int) ($stripeSession->metadata->purchase_id ?? 0);
        $movieSessionId = (int) ($stripeSession->metadata->movie_session_id ?? 0);
        $seatIds = collect(explode(',', (string) ($stripeSession->metadata->seat_ids ?? '')))
            ->map(fn ($seatId) => (int) $seatId)
            ->filter()
            ->unique()
            ->values();

        // Ignore incorrect webhook payloads
        if ($purchaseId <= 0 || $movieSessionId <= 0 || $seatIds->isEmpty()) {
            return;
        }

        $purchase = Purchase::query()->with('payment')->find($purchaseId);
        if (! $purchase) {
            return;
        }

        // If already processed just do nothing
        if ($purchase->status === 'pagado') {
            return;
        }

        // Seat checks, ticket creation, and status updates must be atomic to avoid race conditions
        DB::transaction(function () use ($purchase, $movieSessionId, $seatIds, $stripeSession): void {
            $conflictingSeats = Ticket::query()
                ->where('movie_session_id', $movieSessionId)
                ->whereIn('seat_id', $seatIds)
                ->exists();

            // If seats were sold meanwhile, cancel this purchase
            if ($conflictingSeats) {
                $purchase->update(['status' => 'cancelado']);
                $purchase->payment()?->update([
                    'status' => 'fallido',
                    'gateway_status' => 'asientos_no_disponibles',
                    'stripe_checkout_session_id' => $stripeSession->id,
                    'stripe_payment_intent_id' => is_string($stripeSession->payment_intent) ? $stripeSession->payment_intent : null,
                ]);

                return;
            }

            // Persist one ticket per seat with a unique QR identifier
            foreach ($seatIds as $seatId) {
                Ticket::create([
                    'movie_session_id' => $movieSessionId,
                    'seat_id' => $seatId,
                    'purchase_id' => $purchase->id,
                    'qr_code' => (string) Str::uuid(),
                    'validated' => false,
                ]);
            }

            $purchase->update(['status' => 'pagado']);

            $purchase->payment()?->update([
                'status' => 'correcto',
                'date' => now(),
                'gateway_status' => 'pagado_webhook',
                'stripe_checkout_session_id' => $stripeSession->id,
                'stripe_payment_intent_id' => is_string($stripeSession->payment_intent) ? $stripeSession->payment_intent : null,
            ]);
        });
    }

    // Handle failed or expired Stripe checkout sessions
    private function handleCheckoutFailed(StripeEvent $event): void {
        /** @var \Stripe\Checkout\Session $stripeSession */
        $stripeSession = $event->data->object;
        $purchaseId = (int) ($stripeSession->metadata->purchase_id ?? 0);

        if ($purchaseId <= 0) {
            return;
        }

        $purchase = Purchase::query()->with('payment')->find($purchaseId);
    
        // Never downgrade a purchase that was already confirmed as paid
        if (! $purchase || $purchase->status === 'pagado') {
            return;
        }

        $purchase->update(['status' => 'cancelado']);
        $purchase->payment()?->update([
            'status' => 'fallido',
            'gateway_status' => $event->type,
            'stripe_checkout_session_id' => $stripeSession->id,
            'stripe_payment_intent_id' => is_string($stripeSession->payment_intent) ? $stripeSession->payment_intent : null,
        ]);
    }

    // Accept only seat IDs that belong to the room and are currently not occupied
    private function validatedSeatIds(Request $request, MovieSession $session): array {
        $requestedSeatIds = collect($request->input('seat_ids', []))
            ->map(fn ($seatId) => (int) $seatId)
            ->filter()
            ->unique()
            ->values();

        $availableSeatIds = $session->room->seats
            ->pluck('id')
            ->diff($session->occupiedSeatIds());

        return $requestedSeatIds
            ->filter(fn ($seatId) => $availableSeatIds->contains($seatId))
            ->values()
            ->all();
    }
}