<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'min_stock'
    ];

    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    // Hitung stok saat ini berdasarkan mutasi
    public function getCurrentStockAttribute(): int
    {
        $in = $this->stockMutations()->where('type', 'in')->sum('quantity');
        $out = $this->stockMutations()
            ->whereIn('type', ['used', 'damaged', 'expired'])
            ->sum('quantity');

        return $in - $out;
    }
}
