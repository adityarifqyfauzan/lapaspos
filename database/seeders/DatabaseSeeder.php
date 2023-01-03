<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(OutletSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(PaymentStatusSeeder::class);
        $this->call(OrderStatusSeeder::class);
        $this->call(SupplierSeeder::class);
        // if (App::environment(['local', 'staging'])) {
        //     $this->call(ProductSeeder::class);
        //     $this->call(ProductPriceSeeder::class);
        // }
    }
}
