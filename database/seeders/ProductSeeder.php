<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            [
                'product_name' => 'Laptop ASUS Zephyrus G14',
                'qty' => 12,
                'created_at' => now(),
            ],
            [
                'product_name' => 'Realme Buds T300',
                'qty' => 50,
                'created_at' => now(),
            ],
            [
                'product_name' => 'Mechanical Keyboard Akko 3068',
                'qty' => 20,
                'created_at' => now(),
            ],
        ]);
    }
}
