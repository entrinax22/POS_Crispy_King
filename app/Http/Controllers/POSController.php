<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        return Inertia::render('pos/Index');
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            // ✅ Validate request
            $validated = $request->validate([
                'cart' => 'required|array|min:1',
                'cart.*.product_id' => 'required',
                'cart.*.product_price' => 'required|numeric|min:0',
                'cart.*.product_quantity' => 'required|integer|min:1',
                'total_amount' => 'required|numeric|min:0',
                'orderType' => 'required|in:dine_in,take_out,delivery',
                'status' => 'nullable|in:pending,processing,completed,delivered,cancelled',
            ]);

            // 🔓 Decrypt product IDs
            foreach ($validated['cart'] as &$item) {
                $item['product_id'] = decrypt($item['product_id']);
            }
            unset($item);

            // 🔍 Step 1: Check stock availability
            foreach ($validated['cart'] as $item) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Product not found.");
                }

                if ($product->product_quantity < $item['product_quantity']) {
                    throw new \Exception("Insufficient stock for {$product->product_name}. Available: {$product->product_quantity}");
                }
            }

            // 🧾 Step 2: Create the order
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => auth()->id(),
                'total_amount' => $validated['total_amount'],
                'status' => $validated['status'] ?? 'pending',
                'order_type' => $validated['orderType'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🛒 Step 3: Insert ordered items + deduct stock
            $orderedItems = [];
            foreach ($validated['cart'] as $item) {
                $product = Product::find($item['product_id']);

                // ✅ Deduct stock
                $product->decrement('product_quantity', $item['product_quantity']);

                $orderedItems[] = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['product_quantity'],
                    'price' => $item['product_price'],
                    'total' => $item['product_price'] * $item['product_quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('ordered_items')->insert($orderedItems);

            DB::commit();

            return response()->json([
                "result" => true,
                "message" => "Order created successfully.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => $e->getMessage(),
            ], 500);
        }
    }


}
