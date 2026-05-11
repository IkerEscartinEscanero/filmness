<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Discount extends Model
{
    public const REASON_WELCOME = 'welcome';
    public const REASON_BIRTHDAY = 'birthday';
    public const REASON_LARGE_PURCHASE = 'large_purchase';

    protected $fillable = [
        'user_id',
        'reason',
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

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('active', true)
            ->where(function (Builder $builder) {
                $builder
                    ->whereNull('expiration_date')
                    ->orWhereDate('expiration_date', '>=', now()->toDateString());
            });
    }
}
