<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::updating(function ($order) {
            // Check stock sufficiency when status is changing to 'completed'
            if ($order->isDirty('status') && $order->status === 'completed') {
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->stock < $item->quantity) {
                        throw new \Exception("Insufficient stock for {$item->product->name}. Current stock: {$item->product->stock}");
                    }
                }
            }
        });

        static::updated(function ($order) {
            // Automatically decrement stock when order status changes to 'completed'
            if ($order->wasChanged('status') && $order->status === 'completed') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        // Atomic decrement
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
