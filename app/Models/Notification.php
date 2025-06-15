<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'role', 'product_id', 'title', 'message', 'is_read',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function users()
    {
        return User::where('role', $this->role)->get();
    }

    public function scopeForRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
