<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // --- TAMBAHKAN INI ---
    public $incrementing = false;
    protected $keyType = 'string';
    // --------------------

    protected $fillable = [
        'id', // Tambahkan 'id' karena kita akan mengisinya secara manual
        'user_id', 
        'total_amount', 
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    protected static function booted()
{
    static::updated(function ($order) {
        if ($order->status === 'completed') {
            foreach ($order->products as $product) {
                $product->increment('sold', $product->pivot->quantity);
                $product->save();
            }
        }
    });
}
public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
