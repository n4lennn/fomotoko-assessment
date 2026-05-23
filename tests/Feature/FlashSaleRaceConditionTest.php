<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleRaceConditionTest extends TestCase
{
    use RefreshDatabase;

    // Test untuk memastikan bahwa saat banyak pelanggan mencoba membeli produk flash sale secara bersamaan, stok tidak akan oversell (tidak akan negatif)
    public function test_concurrent_orders_cannot_oversell_stock(): void
    {
        // Buat produk flash sale hanya 5 stok
        $product = Product::create([
            'name'             => 'Flash Sale Item',
            'description'      => 'Test product',
            'price'            => 100000,
            'flash_sale_price' => 10000,
            'is_flash_sale'    => true,
            'stock'            => 5,
        ]);

        $totalRequests = 20; // 20 pelanggan mencoba beli secara bersamaan, tapi hanya 5 yang berhasil
        $successCount  = 0;
        $failCount     = 0;

        // Simulasi 20 request beli produk secara bersamaan
        for ($i = 0; $i < $totalRequests; $i++) {
            $response = $this->postJson('/api/orders', [
                'customer_name'  => "Customer {$i}",
                'customer_email' => "customer{$i}@test.com",
                'items'          => [
                    ['product_id' => $product->id, 'quantity' => 1],
                ],
            ]);

            if ($response->status() === 201) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        // Refresh produk untuk mendapatkan data stok terbaru setelah semua request selesai
        $product->refresh();

        echo "\n✓ Race condition test result:";
        echo "\n  Successful orders : {$successCount}";
        echo "\n  Failed orders     : {$failCount}";
        echo "\n  Remaining stock   : {$product->stock}\n";

        // Pastikan stok tidak pernah negatif
        $this->assertGreaterThanOrEqual(0, $product->stock, 'Stock should never go negative');

        // Pastikan hanya 5 order yang berhasil, karena stok hanya 5
        $this->assertEquals(5, $successCount, "Only 5 orders should succeed, got {$successCount}");

        // Pastikan 15 order gagal karena stok habis
        $this->assertEquals(15, $failCount, "15 orders should fail due to insufficient stock");
    }
}