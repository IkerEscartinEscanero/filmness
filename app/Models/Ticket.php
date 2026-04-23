<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model {
    protected $fillable = ['movie_session_id', 'seat_id', 'purchase_id', 'qr_code', 'validated'];

    public function movieSession() {
        return $this->belongsTo(MovieSession::class);
    }

    public function seat() {
        return $this->belongsTo(Seat::class);
    }
}
