<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            [
                'name' => 'Nasi Goreng',
                'description' => 'Nasi Goreng Biasa'
            ],
            [
                'name' => 'Nasi Kuning',
                'description' => 'Nasi Kuning Biasa'
            ],
            [
                'name' => 'Nasi Lengkoh',
                'description' => 'Nasi Lengkoh Biasa'
            ],
            [
                'name' => 'Teh Botol Sosro',
                'description' => 'Apapun makanan nya, minumnya tetep teh kotak'
            ],
            [
                'name' => 'Teh Kotak',
                'description' => 'Apapun makanan nya, minumnya tetep teh kotak'
            ],
            [
                'name' => 'Tehjus',
                'description' => 'Apapun makanan nya, minumnya tetep teh kotak'
            ],
        ];

        foreach ($data as $value) {
            Product::create($value);
        }

        ProductCategory::create([
            "product_id" =>  1,
            "category_id" =>  1,
        ]);

        ProductCategory::create([
            "product_id" =>  1,
            "category_id" =>  2,
        ]);

        ProductCategory::create([
            "product_id" =>  2,
            "category_id" =>  1,
        ]);

        ProductCategory::create([
            "product_id" =>  2,
            "category_id" =>  2,
        ]);

        ProductCategory::create([
            "product_id" =>  3,
            "category_id" =>  1,
        ]);

        ProductCategory::create([
            "product_id" =>  4,
            "category_id" =>  3,
        ]);

        ProductCategory::create([
            "product_id" =>  5,
            "category_id" =>  3,
        ]);

        ProductCategory::create([
            "product_id" =>  6,
            "category_id" =>  3,
        ]);

    }
}
