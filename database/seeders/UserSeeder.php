<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
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
                'name' => 'Administrator',
                'username' => 'administrator',
                'email' => 'administrator@gmail.com',
                'password' => 'password',
                'role_id' => 1
            ],
            [
                'name' => 'Kasir',
                'username' => 'kasir',
                'email' => 'kasir@gmail.com',
                'password' => 'password',
                'role_id' => 2
            ],
        ];

        foreach ($data as $value) {
            User::create($value);
        }
    }
}
