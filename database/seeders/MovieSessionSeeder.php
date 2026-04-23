<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\MovieSession;
use App\Models\Room;
use Illuminate\Database\Seeder;

class MovieSessionSeeder extends Seeder {
    /**
    * Creates initial sessions for a single bootstrap movie
    * After that, new sessions should be managed from the admin UI
    */
    public function run(): void {
        $film = Film::query()->orderBy('id')->first();
        $rooms = Room::all();

        if (! $film || $rooms->isEmpty()) {
            $this->command->warn('No hay película inicial o salas. Ejecuta primero FilmSeeder y RoomSeeder.');
            return;
        }

        if (MovieSession::where('film_id', $film->id)->exists()) {
            $this->command->info('La película inicial ya tiene sesiones.');
            return;
        }

        $times = ['17:00', '19:30', '22:00'];
        $price = 9.50;
        $room = $rooms->first();

        for ($day = 0; $day < 7; $day++) {
            $date = now()->addDays($day);

            foreach ($times as $time) {
                [$hour, $minute] = explode(':', $time);
                MovieSession::create([
                    'film_id' => $film->id,
                    'room_id' => $room->id,
                    'date'    => $date->copy()->setTime((int) $hour, (int) $minute),
                    'price'   => $price,
                ]);
            }
        }
    }
}