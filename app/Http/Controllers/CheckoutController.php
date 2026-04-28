<?php

namespace App\Http\Controllers;

use App\Models\MovieSession;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class CheckoutController extends Controller {
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

        $purchase = Purchase::create([
            'user_id' => Auth::id(),
            'contact_email' => $validated['email'],
            'total' => $total,
            'status' => 'pendiente',
        ]);

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