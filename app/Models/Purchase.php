<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model {
    protected $fillable = [
        'user_id',
        'contact_email',
        'total',
        'status',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}