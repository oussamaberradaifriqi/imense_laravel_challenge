<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductService;

class ImportProducts extends Command
{
    protected $signature = 'import:products';
    protected $description = 'Imports products into the database';
    protected $productService;

    public function __construct(ProductService $productService)
    {
        parent::__construct();
        $this->productService = $productService;
    }

    public function handle()
    {
        $filePath = storage_path('app/products.csv');
        $count = $this->productService->importProducts($filePath);
        $this->info("Imported {$count} products.");
    }
}

