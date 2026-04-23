<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieSession extends Model {
    protected $fillable = ['film_id', 'room_id', 'date', 'price'];

    protected $casts = [
        'date'  => 'datetime',
        'price' => 'decimal:2',
    ];

    public function film() {
        return $this->belongsTo(Film::class);
    }

    public function room() {
        return $this->belongsTo(Room::class)->with('seats');
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }

    /** Returns the IDs of seats already taken for this session. */
    public function occupiedSeatIds(): array {
        return $this->tickets()->pluck('seat_id')->toArray();
    }
}