<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Support\Collection;

class DiscountService {
    private const DEFAULT_PERCENTAGE = 10;
    public const LARGE_PURCHASE_THRESHOLD = 50.0;

    public function ensureWelcomeDiscount(User $user): Discount {
        return Discount::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'reason' => Discount::REASON_WELCOME,
            ],
            [
                'type' => 'porcentaje',
                'value' => self::DEFAULT_PERCENTAGE,
                'active' => true,
            ]
        );
    }

    public function ensureBirthdayDiscount(User $user): ?Discount {
        if (! $user->birth_date) {
            return null;
        }

        if ($user->birth_date->format('m-d') !== now()->format('m-d')) {
            return null;
        }

        return Discount::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'reason' => Discount::REASON_BIRTHDAY,
                'expiration_date' => now()->toDateString(),
            ],
            [
                'type' => 'porcentaje',
                'value' => self::DEFAULT_PERCENTAGE,
                'active' => true,
            ]
        );
    }

    public function grantLargePurchaseDiscount(User $user): Discount {
        return Discount::query()->create([
            'user_id' => $user->id,
            'reason' => Discount::REASON_LARGE_PURCHASE,
            'type' => 'porcentaje',
            'value' => self::DEFAULT_PERCENTAGE,
            'active' => true,
        ]);
    }

    public function availableDiscountsForUser(?User $user): Collection {
        if (! $user) {
            return collect();
        }

        $this->ensureBirthdayDiscount($user);

        return Discount::query()
            ->where('user_id', $user->id)
            ->available()
            ->orderBy('expiration_date')
            ->get();
    }

    public function availableDiscountsForCheckout(?User $user, float $subtotal): Collection {
        return $this->availableDiscountsForUser($user)
            ->filter(function (Discount $discount) use ($subtotal, $user) {
                if ($discount->reason === Discount::REASON_BIRTHDAY) {
                    return $user?->birth_date && $user->birth_date->format('m-d') === now()->format('m-d');
                }

                if ($discount->reason === Discount::REASON_LARGE_PURCHASE) {
                    return $subtotal >= self::LARGE_PURCHASE_THRESHOLD;
                }

                return true;
            })
            ->values();
    }

    public function resolveCheckoutDiscount(?User $user, ?int $discountId, float $subtotal): ?Discount {
        if (! $user || ! $discountId) {
            return null;
        }

        $discount = Discount::query()
            ->where('user_id', $user->id)
            ->available()
            ->find($discountId);

        if (! $discount) {
            return null;
        }

        if ($this->calculateAmount($discount, $subtotal) <= 0) {
            return null;
        }

        return $discount;
    }

    public function resolveCheckoutDiscounts(?User $user, array $discountIds, float $subtotal): Collection {
        if (! $user || count($discountIds) === 0) {
            return collect();
        }

        $ids = collect($discountIds)
            ->map(fn ($discountId) => (int) $discountId)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Discount::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->available()
            ->get()
            ->filter(function (Discount $discount) use ($subtotal, $user) {
                if ($discount->reason === Discount::REASON_BIRTHDAY) {
                    return $user?->birth_date && $user->birth_date->format('m-d') === now()->format('m-d');
                }

                if ($discount->reason === Discount::REASON_LARGE_PURCHASE && $subtotal < self::LARGE_PURCHASE_THRESHOLD) {
                    return false;
                }

                return $this->calculateAmount($discount, $subtotal) > 0;
            })
            ->values();
    }

    public function calculateAmount(?Discount $discount, float $subtotal): float {
        if (! $discount) {
            return 0.0;
        }

        if ($discount->type === 'porcentaje') {
            return round($subtotal * (((float) $discount->value) / 100), 2);
        }

        return min($subtotal, round((float) $discount->value, 2));
    }

    public function calculateTotalDiscountAmount(Collection $discounts, float $subtotal): float {
        $amount = $discounts
            ->sum(fn (Discount $discount) => $this->calculateAmount($discount, $subtotal));

        return min(round($subtotal, 2), round((float) $amount, 2));
    }

    public function consume(?Discount $discount): void {
        if (! $discount) {
            return;
        }

        // The large purchase discount is reusable while it remains active.
        if ($discount->reason === Discount::REASON_LARGE_PURCHASE) {
            return;
        }

        $discount->update(['active' => false]);
    }

    public function labelFor(?string $reason): string {
        return match ($reason) {
            Discount::REASON_WELCOME => 'Descuento de bienvenida',
            Discount::REASON_BIRTHDAY => 'Descuento de cumpleaños',
            Discount::REASON_LARGE_PURCHASE => 'Descuento por compra superior a 50€',
            default => 'Descuento disponible',
        };
    }
}