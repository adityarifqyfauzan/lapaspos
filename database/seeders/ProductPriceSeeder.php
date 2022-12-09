<?php

namespace Database\Seeders;

use App\Models\ProductPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductPrice::create([
            'product_id' => 1,
            'base_price' => 3000,
            'margin' => 2000,
        ]);

        ProductPrice::create([
            'product_id' => 2,
            'base_price' => 4000,
            'margin' => 1000,
        ]);

        ProductPrice::create([
            'product_id' => 3,
            'base_price' => 4000,
            'margin' => 1000,
        ]);

        ProductPrice::create([
            'product_id' => 4,
            'base_price' => 8000,
            'margin' => 2000,
        ]);

        ProductPrice::create([
            'product_id' => 5,
            'base_price' => 8000,
            'margin' => 2000,
        ]);

        ProductPrice::create([
            'product_id' => 6,
            'base_price' => 2000,
            'margin' => 1000,
        ]);
    }
}
