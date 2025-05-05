<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_product';
    protected $fillable = [
        'id_category',
        'name',
        'description',
        'total_product_detail'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function productDetails(): HasMany
    {
        return $this->hasMany(ProductDetail::class, 'product_id');
    }
}
