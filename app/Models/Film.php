<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model {
    public function movieSessions() {
        return $this->hasMany(MovieSession::class)->orderBy('date');
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    protected $fillable = [
        'title',
        'logo',
        'poster',
        'release_date',
        'director',
        'genre',
        'distribution',
        'synopsis',
        'duration',
        'trailer_url'
    ];
}
