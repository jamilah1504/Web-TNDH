<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Category;

class ProductObserver
{
    public function created(Product $product)
    {
        $this->updateCategoryTotalProduct($product->id_category);
    }

    public function updated(Product $product)
    {
        // Perbarui total produk untuk kategori lama dan baru jika id_category berubah
        $originalCategoryId = $product->getOriginal('id_category');
        if ($originalCategoryId !== $product->id_category) {
            $this->updateCategoryTotalProduct($originalCategoryId);
            $this->updateCategoryTotalProduct($product->id_category);
        }
    }

    public function deleted(Product $product)
    {
        $this->updateCategoryTotalProduct($product->id_category);
    }

    private function updateCategoryTotalProduct($categoryId)
    {
        if ($categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $category->total_product = $category->products()->count();
                $category->save();
            }
        }
    }
}
