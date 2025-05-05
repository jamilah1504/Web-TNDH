<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_category';
    protected $fillable = [
        'name_category',
        'total_product'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id_category');
    }
}
