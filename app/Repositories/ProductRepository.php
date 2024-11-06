<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductVariation;

class ProductRepository implements ProductRepositoryInterface
{
    public function getProductById($id)
    {
        return Product::find($id);
    }

    public function updateOrCreateProduct($data)
    {
        return Product::updateOrCreate(
            ['id' => $data['id']],
            [
                'name' => $data['name'],
                'sku' => $data['sku'],
                'status' => $data['status'],
                'price' => $data['price'],
                'currency' => $data['currency']
            ]
        );
    }

    public function deleteMissingProducts(array $existingIds)
    {
        Product::whereNotIn('id', $existingIds)
            ->whereNull('deleted_at') 
            ->update([
                'deleted_at' => now(),
                'deleted_reason' => 'Deleted due to synchronization'
            ]);
    }

    public function createProductVariation($productId, $variation)
    {
        return ProductVariation::create([
            'product_id' => $productId,
            'variation_type' => $variation['type'],
            'value' => $variation['value'],
            'quantity' => $variation['quantity']
        ]);
    }
}
