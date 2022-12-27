<?php

namespace Database\Seeders;

use App\Models\PaymentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentStatusSeeder extends Seeder
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
                "name" => "Pending"
            ],
            [
                "name" => "Sukses"
            ],
            [
                "name" => "Gagal"
            ],
        ];
        foreach ($data as $value) {
            PaymentStatus::create($value);
        }
    }
}
