<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    // Relationship dengan Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship dengan Product (jika ada model Product)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor untuk format price
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 2, ',', '.');
    }

    // Accessor untuk subtotal
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    // Accessor untuk format subtotal
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 2, ',', '.');
    }
}