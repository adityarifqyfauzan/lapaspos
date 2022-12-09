<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
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
                'name' => 'makanan'
            ],
            [
                'name' => 'pedas'
            ],
            [
                'name' => 'minuman'
            ]
        ];

        foreach ($data as $value) {
            Category::create($value);
        }
    }
}
