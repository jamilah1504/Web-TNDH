<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // 1. Buat kolom 'order_id' dengan tipe data string (VARCHAR)
            $table->string('order_id'); 
            
            // 2. Definisikan foreign key constraint secara manual
            $table->foreign('order_id')
                  ->references('id')      // Mengacu ke kolom 'id'
                  ->on('orders')          // di tabel 'orders'
                  ->onDelete('cascade'); // Hapus item jika order dihapus
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['completed', 'failed'])->default('completed');
            $table->timestamp('payment_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};