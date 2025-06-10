<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_category')
                ->constrained('categories', 'id_category')
                ->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('excerpt');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->enum('stock_status', ['available', 'out_of_stock'])->default('available');
            $table->string('image')->nullable()->default('default.jpg');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};