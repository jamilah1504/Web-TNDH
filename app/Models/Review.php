<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'product_id', 'rating', 'comment', 'is_approved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    protected static function booted()
{
    static::updated(function ($review) {
        if ($review->isDirty('is_approved') && $review->is_approved) {
            $product = $review->product;
            $rating = $product->reviews()->where('is_approved', true)->avg('rating');
            $product->update(['rating' => $rating ?? 0]);
        }
    });
}
}
