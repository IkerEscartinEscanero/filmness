<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Film;
use App\Models\MovieSession;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Review;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoProfileSeeder extends Seeder {
    /**
     * Seed sample profile data without affecting other users.
     */
    public function run(): void {
        $user = User::firstOrCreate(
            ['email' => 'test@filmness.com'],
            [
                'name' => 'Test',
                'password' => Hash::make('test123456789'),
                'birth_date' => '2000-06-20',
                'role' => 'user',
            ]
        );

        $film = Film::query()->orderBy('id')->first();
        $room = Room::query()->orderBy('id')->first();

        if (!$film || !$room) {
            return;
        }

        $pastSession = MovieSession::query()->firstOrCreate(
            [
                'film_id' => $film->id,
                'room_id' => $room->id,
                'date' => now()->subDays(10)->setTime(20, 0),
            ],
            [
                'price' => 9.50,
            ]
        );

        $purchase = Purchase::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'contact_email' => $user->email,
                'status' => 'pagado',
                'total' => 19.00,
            ]
        );

        Payment::query()->updateOrCreate(
            ['purchase_id' => $purchase->id],
            [
                'payment_method' => 'stripe',
                'status' => 'correcto',
                'date' => now()->subDays(10),
                'gateway_status' => 'pagado_webhook',
            ]
        );

        $seatIds = $room->seats()->orderBy('row')->orderBy('number')->limit(2)->pluck('id');

        foreach ($seatIds as $seatId) {
            Ticket::query()->updateOrCreate(
                [
                    'movie_session_id' => $pastSession->id,
                    'seat_id' => $seatId,
                ],
                [
                    'purchase_id' => $purchase->id,
                    'qr_code' => (string) Str::uuid(),
                    'validated' => true,
                ]
            );
        }

        Review::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'film_id' => $film->id,
            ],
            [
                'comment' => 'Muy entretenida y perfecta para verla en grupo.',
                'stars' => 4,
                'date' => now()->subDays(8)->toDateString(),
            ]
        );

        Discount::query()->where('user_id', $user->id)->delete();

        Discount::query()->create([
            'user_id' => $user->id,
            'reason' => Discount::REASON_WELCOME,
            'type' => 'porcentaje',
            'value' => 10,
            'active' => true,
        ]);

        Discount::query()->create([
            'user_id' => $user->id,
            'reason' => Discount::REASON_BIRTHDAY,
            'type' => 'porcentaje',
            'value' => 10,
            'active' => true,
            'expiration_date' => now()->toDateString(),
        ]);

        Discount::query()->create([
            'user_id' => $user->id,
            'reason' => Discount::REASON_LARGE_PURCHASE,
            'type' => 'porcentaje',
            'value' => 10,
            'active' => true,
        ]);
    }
}