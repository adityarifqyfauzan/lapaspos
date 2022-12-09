<?php

namespace Tests\Unit;

use App\Services\ProductService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $product_service = new ProductService();
        $products = $product_service->findBy([], 0, 0);

        foreach ($products as $product) {
            Log::debug($product);
        }
    }
}
