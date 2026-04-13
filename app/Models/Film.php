<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
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
