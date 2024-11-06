<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductService;

class SynchronizeProducts extends Command
{
    protected $signature = 'products:synchronize';
    protected $description = 'Synchronize products with external API';

    protected $productService;

    public function __construct(ProductService $productService)
    {
        parent::__construct();
        $this->productService = $productService;
    }

    public function handle()
    {
        $this->productService->synchronizeWithApi();
        $this->info('Product synchronization complete.');
    }
}
