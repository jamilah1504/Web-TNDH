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

        $category = Category::create(['name_category' => 'Makanan']);
        $product = Product::create([
            'id_category' => $category->id_category,
            'name' => 'Nasi Goreng',
            'excerpt' => 'Nasi goreng spesial',
            'price' => 25000,
            'stock_status' => 'available',
            'is_available' => true,
        ]);

        $user = User::create([
            'name' => 'Customer',
            'email' => 'customer@tndhfood.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '081234567810',
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 25000,
            'status' => 'completed',
        ]);

        $order->products()->attach($product->id, ['quantity' => 1, 'price' => 25000]);

        Payment::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'amount' => 25000,
            'status' => 'completed',
            'payment_date' => now(),
        ]);

        Notification::create([
            'role' => 'admin',
            'title' => 'Test Notifikasi',
            'message' => 'Ini adalah notifikasi untuk admin.',
            'is_read' => false,
        ]);

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Delicious!',
            'is_approved' => false,
        ]);

        Slider::create([
            'title' => 'Welcome Offer',
            'image' => 'sliders/welcome.jpg',
            'description' => 'New menu available!',
            'is_active' => true,
        ]);
    }
}
