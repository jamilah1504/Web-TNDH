<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Konfigurasi untuk ID string (misal: UUID atau ID unik dari Midtrans)
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id'; // Eksplisit mendefinisikan primary key

    protected $fillable = [
        'id',
        'user_id', 
        'total_amount', 
        'status',
    ];

    /**
     * Relasi ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Payment.
     */
    public function review()
    {
        // Asumsi foreign key di tabel 'reviews' adalah 'id_orderItems'
        return $this->hasOne(Review::class, 'id_orderItems');
    }

    /**
     * Relasi ke produk
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relasi ke OrderItem. Ini adalah relasi yang akan kita gunakan di mana-mana.
     * Relasi ini akan mengembalikan semua item di dalam sebuah pesanan.
     */
    public function orderItems()
    {
        // Parameter kedua ('order_id') dan ketiga ('id') adalah foreign key dan local key.
        // Seharusnya Laravel bisa mendeteksinya secara otomatis, tapi lebih aman menuliskannya secara eksplisit
        // saat menggunakan primary key non-standar.
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * Logika yang dijalankan setelah order di-update.
     */
    protected static function booted()
    {
        static::updated(function ($order) {
            // Jika status order 'completed', update jumlah produk terjual.
            if ($order->isDirty('status') && $order->status === 'completed') {
                // Gunakan relasi orderItems yang sudah kita standarisasi
                foreach ($order->orderItems as $item) {
                    // Akses produk dari setiap item, lalu update jumlah terjualnya.
                    $product = $item->product;
                    if ($product) {
                        $product->increment('sold', $item->quantity);
                    }
                }
            }
        });
    }
}