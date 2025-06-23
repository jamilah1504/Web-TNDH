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
            'email' => 'admin@tndhfood.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        Notification::create([
            'role' => 'admin',
            'title' => 'Test Notifikasi',
            'message' => 'Ini adalah notifikasi untuk admin.',
            'is_read' => false,
        ]);

        Slider::create([
            'title' => 'Welcome Offer',
            'image' => 'sliders/welcome.jpg',
            'description' => 'New menu available!',
            'is_active' => true,
        ]);
    }
}
