<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    protected $fillable = [
        'purchase_id',
        'payment_method',
        'status',
        'date',
        'gateway_status',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}