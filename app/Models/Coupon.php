<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_percentage',
        'discount_amount',
        'valid_until',
        'usage_limit',
        'used_count'
    ];

    /**
     * Check if the coupon is valid.
     */
    public function isValid()
    {
        if ($this->valid_until && $this->valid_until < now()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount for a given total price.
     */
    public function calculateDiscount($totalPrice)
    {
        if ($this->discount_percentage) {
            return ($totalPrice * $this->discount_percentage) / 100;
        }

        if ($this->discount_amount) {
            return min($this->discount_amount, $totalPrice);
        }

        return 0;
    }
}
