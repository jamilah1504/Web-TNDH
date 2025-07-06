<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'title', 'message', 'photo', 'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function users()
    {
        return User::all();
    }
}
