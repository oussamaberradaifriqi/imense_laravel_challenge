<?php

namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function getProductById($id);
    public function updateOrCreateProduct($data);
    public function deleteMissingProducts(array $existingIds);
    public function createProductVariation($productId, $variation);
}
