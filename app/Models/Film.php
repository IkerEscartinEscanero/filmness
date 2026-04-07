<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Film extends Authenticatable
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
        'trailer'
    ];
}
