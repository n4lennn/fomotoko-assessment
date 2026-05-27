<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // list semua produk
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }

    // satu produk detail
    public function show(Product $product): JsonResponse
    {
        return response()->json($product, 200);
    }

    //menambah produk baru
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'is_flash_sale' => 'boolean',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($validatedData);
        return response()->json($product, 201);
    }

    // mengupdate produk (e.g. toogle flash sale)
    public function update(Request $request, Product $product): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'is_flash_sale' => 'boolean',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        $product->update($validatedData);
        return response()->json($product, 200);
    }
}
