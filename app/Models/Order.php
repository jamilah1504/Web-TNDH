<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Order extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'total_amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'id_orderItems');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    protected static function booted()
    {
        // 👇 Ini untuk saat order BARU dibuat
        static::created(function ($order) {
            if (in_array($order->status, ['completed', 'failed'])) {
                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id' => $order->user_id,
                        'amount' => $order->total_amount,
                        'status' => $order->status,
                        'payment_date' => now(),
                    ]
                );
            }
        });

        // 👇 Ini untuk saat status order DIPERBARUI ke completed/failed
        static::updated(function ($order) {
            if ($order->isDirty('status') && in_array($order->status, ['completed', 'failed'])) {
                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id' => $order->user_id,
                        'amount' => $order->total_amount,
                        'status' => $order->status,
                        'payment_date' => now(),
                    ]
                );
            }

            // Update stok terjual jika order completed
            if ($order->isDirty('status') && $order->status === 'completed') {
                foreach ($order->orderItems as $item) {
                    $product = $item->product;
                    if ($product) {
                        $product->increment('sold', $item->quantity);
                    }
                }
            }
        });
    }
}
