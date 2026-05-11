<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'value',
        'active',
        'expiration_date',
    ];

    protected $casts = [
        'active' => 'boolean',
        'expiration_date' => 'date',
        'value' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
