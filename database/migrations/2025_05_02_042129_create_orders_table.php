<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled','settlement','challenge','expired','denied'])->default('pending');
            $table->timestamps();
        });

        // Schema::create('order_product', function (Blueprint $table) {
        //     $table->id();
        //     // 1. Buat kolom 'order_id' dengan tipe data string (VARCHAR)
        //     $table->string('order_id'); 
            
        //     // 2. Definisikan foreign key constraint secara manual
        //     $table->foreign('order_id')
        //           ->references('id')      // Mengacu ke kolom 'id'
        //           ->on('orders')          // di tabel 'orders'
        //           ->onDelete('cascade');
        //     $table->foreignId('product_id')->constrained()->onDelete('cascade');
        //     $table->integer('quantity')->default(1);
        //     $table->decimal('price', 10, 2);
        //     $table->timestamps();
        // });
    }

    public function down(): void
    {
        // Schema::dropIfExists('order_product');
        Schema::dropIfExists('orders');
    }
};