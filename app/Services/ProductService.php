<?php

namespace App\Services;

use App\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\Http;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function importProducts($filePath)
    {
        $contents = file_get_contents($filePath);
        $lines = explode("\n", $contents);
        $existingIds = [];

        // Skip the header
        $lines = array_slice($lines, 1);

        foreach ($lines as $line) {
            // Skip empty lines
            if (trim($line) === '') {
                continue;
            }

            $fields = str_getcsv($line);

            // Validate and handle 'NULL' or invalid price values
            $price = ($fields[3] === 'NULL' || !is_numeric($fields[3])) ? null : (float) $fields[3];

            // Skip products with invalid prices (if price is null or less than 0)
            if ($price === null || $price < 0) {
                continue;
            }

            // Handle 'NULL' or empty SKU
            $sku = $fields[2] === '' ? null : $fields[2];

            $productData = [
                'id' => $fields[0],
                'name' => $fields[1],
                'sku' => $sku,
                'price' => $price,
                'currency' => $fields[4],
                'status' => $fields[7]
            ];

            $product = $this->productRepository->updateOrCreateProduct($productData);
            $existingIds[] = $product->id;

            if (!empty($fields[6])) {
                // Decode variations and check if it's an array
                $variations = json_decode($fields[6], true);
                if (is_array($variations)) {
                    foreach ($variations as $variation) {
                        $this->productRepository->createProductVariation($product->id, [
                            'type' => $variation['name'],
                            'value' => $variation['value'],
                            'quantity' => $variation['quantity'] ?? 0
                        ]);
                    }
                }
            }
            sleep(2);
        }

        $this->productRepository->deleteMissingProducts($existingIds);
        return count($existingIds);
    }





    public function synchronizeWithApi()
    {
        $response = Http::get('https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products');
        $products = $response->json();

        $existingIds = [];

        foreach ($products as $productData) {
            $productDetails = [
                'id' => $productData['id'],
                'name' => $productData['name'],
                'sku' => $productData['sku'] ?? null,
                'price' => $productData['price'],
                'currency' => 'SAR', // by default i suppose it SAR
                'status' => 'active' //  status as active for synced products
            ];

            $product = $this->productRepository->updateOrCreateProduct($productDetails);
            $existingIds[] = $product->id;

            foreach ($productData['variations'] as $variation) {
                $this->productRepository->createProductVariation($product->id, [
                    'type' => 'color', // Assuming variation type is color
                    'value' => $variation['color'],
                    'quantity' => $variation['quantity'] ?? 0
                ]);
            }

            // 2s delay
            sleep(2);
        }

        // Soft delete products that are no longer available in the API response
        $this->productRepository->deleteMissingProducts($existingIds);
    }
}
