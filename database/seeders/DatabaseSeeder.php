<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Slider;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'addres' => 'Jalan No. 123',
            'is_active' => true,
        ]);
    }
}
