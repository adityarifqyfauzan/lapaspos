<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
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
                'name' => 'Belum dibayar',
            ],
            [
                'name' => 'Lunas',
            ],
            [
                'name' => 'Dibatalkan',
            ],
        ];

        foreach ($data as $value) {
            OrderStatus::create($value);
        }
    }
}
