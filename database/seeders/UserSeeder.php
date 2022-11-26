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
                'password' => bcrypt('password'),
                'role_id' => 1
            ]
        ];

        foreach ($data as $value) {
            User::create($value);
        }
    }
}
