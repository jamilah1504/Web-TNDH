<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            // 1. Buat kolom 'order_id' dengan tipe data string (VARCHAR)
            $table->string('order_id'); 
            
            // 2. Definisikan foreign key constraint secara manual
            $table->foreign('order_id')
                  ->references('id')      // Mengacu ke kolom 'id'
                  ->on('orders')          // di tabel 'orders'
                  ->onDelete('cascade'); // Hapus item jika order dihapus
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};