<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = [
        'id_category', 'name', 'description', 'excerpt', 'price', 'discount_price',
        'stock_quantity', 'image', 'is_available', 'stock_status', 'rating', 'sold',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getEffectivePriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getStockStatusDisplayAttribute()
    {
        return $this->stock_status === 'available' ? 'Tersedia' : 'Habis';
    }

    public function getIsAvailableDisplayAttribute()
    {
        return $this->is_available ? 'Masih Dijual' : 'Tidak Dijual';
    }

    protected static function booted()
    {
        static::updating(function ($product) {
            Log::info('Updating product: ' . $product->id . ', Stock Status: ' . $product->stock_status . ', Is Available: ' . $product->is_available);
        });
    }

}
