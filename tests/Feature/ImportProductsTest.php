<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;

class ImportProductsTest extends TestCase
{
    public function test_product_import()
    {
        $this->artisan('import:products')->assertExitCode(0);
        $this->assertDatabaseHas('products', ['name' => 'code 209']);
    }
}
