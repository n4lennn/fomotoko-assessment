<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Sepatu Flash Sale',
            'description' => 'Sepatu yang bagus sekali dengan diskon besar selama flash sale!',
            'price' => 300000,
            'flash_sale_price' => 50000,
            'is_flash_sale' => true,
            'stock' => 11, // 11 produk tersedia
        ]);
    }
}
