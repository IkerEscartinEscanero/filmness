<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
    * Creates 3 rooms with their seats (rows A-H, 10 seats per row)
    */
    public function run(): void
    {
        $rooms = [
            ['name' => 'Sala 1', 'capacity' => 80],
            ['name' => 'Sala 2', 'capacity' => 80],
            ['name' => 'Sala 3', 'capacity' => 80],
        ];

        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $seatsPerRow = 10;

        foreach ($rooms as $roomData) {
            $room = Room::create($roomData);

            foreach ($rows as $row) {
                for ($n = 1; $n <= $seatsPerRow; $n++) {
                    Seat::create([
                        'room_id' => $room->id,
                        'row'     => $row,
                        'number'  => $n,
                    ]);
                }
            }
        }
    }
}
