<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // list semua order dengan detail itemnya
    public function index(): JsonResponse
    {
        $orders = Order::with('orderItems.product')->get();
        return response()->json($orders, 200);
    }

    // detail satu order dengan itemnya
    public function show(Order $order): JsonResponse
    {
        $order->load('orderItems.product');
        return response()->json($order, 200);
    }

    // membuat order baru. pakai transaksi db dan (lockForUpdate) untuk mencegah race condition saat flash sale
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name'         => 'required|string|max:255',
            'customer_email'        => 'required|email',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|integer|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                $totalPrice = 0;
                $orderItemsData = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product: {$product->name}", 422);
                    }

                    $product->decrement('stock', $item['quantity']);

                    $effectivePrice = $product->getEffectivePrice();
                    $totalPrice += $effectivePrice * $item['quantity'];

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $effectivePrice,
                    ];
                }

                $order = Order::create([
                    'customer_name'  => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'total_price'    => $totalPrice,
                    'status'         => 'confirmed',
                ]);

                $order->orderItems()->createMany($orderItemsData);

                return $order->load('orderItems.product');
            });

            return response()->json($order, 201);

        } catch (\Exception $e) {
            $code = $e->getCode() === 422 ? 422 : 500;
            return response()->json(['message' => $e->getMessage()], $code);
        }
    }
}