<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Mass assignment fields yang boleh diisi
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    // Casting untuk memastikan data sesuai tipe
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    // Eager load relasi secara default agar efisien saat query
    protected $with = ['product'];

    // Relasi ke Order (Setiap item milik satu order)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Product (Setiap item berkaitan dengan satu produk)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor untuk format harga (contoh: Rp 10.000,00)
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 2, ',', '.');
    }

    // Accessor subtotal = price * quantity
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    // Accessor untuk format subtotal (contoh: Rp 20.000,00)
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 2, ',', '.');
    }
}
