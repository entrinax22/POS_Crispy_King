<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
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

            if($request->has('order_id')){
                // If order_id is present, update the existing order
                $validated = $request->validate([
                    'order_id' => 'required|integer|exists:orders,order_id',
                    'cart' => 'required|array|min:1',
                    'cart.*.product_id' => 'required',
                    'cart.*.product_price' => 'required|numeric|min:0',
                    'cart.*.product_quantity' => 'required|integer|min:1',
                    'total_amount' => 'required|numeric|min:0',
                    'orderType' => 'required|in:dine_in,take_out',
                ]);

                // Decrypt all product_ids in the cart
                foreach ($validated['cart'] as &$item) {
                    $item['product_id'] = decrypt($item['product_id']);
                }
                unset($item);

                // Update the existing order
                DB::table('orders')->where('order_id', $validated['order_id'])->update([
                    'total_amount' => $validated['total_amount'],
                    'status' => 'pending',
                    'order_type' => $validated['orderType'],
                    'updated_at' => now(),
                ]);

                // Delete existing ordered items for this order
                DB::table('ordered_items')->where('order_id', $validated['order_id'])->delete();

                // Insert new ordered items
                $orderedItems = [];
                foreach ($validated['cart'] as $item) {
                    $orderedItems[] = [
                        'order_id' => $validated['order_id'],
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
                    "message" => "Order updated successfully.",
                ]);
            }else{
                // Validate the request data
                $validated = $request->validate([
                    'cart' => 'required|array|min:1',
                    'cart.*.product_id' => 'required',
                    'cart.*.product_price' => 'required|numeric|min:0',
                    'cart.*.product_quantity' => 'required|integer|min:1',
                    'total_amount' => 'required|numeric|min:0',
                    'orderType' => 'required|in:dine_in,take_out',
                ]);

                // Decrypt all product_ids in the cart
                foreach ($validated['cart'] as &$item) {
                    $item['product_id'] = decrypt($item['product_id']);
                }
                unset($item);

                // Create a new order (one order per request)
                $orderId = DB::table('orders')->insertGetId([
                    'user_id' => auth()->id(),
                    'total_amount' => $validated['total_amount'],
                    'status' => 'pending',
                    'order_type' => $validated['orderType'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Insert ordered items
                $orderedItems = [];
                foreach ($validated['cart'] as $item) {
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
            }

            

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function update($order_id, Request $request)
    {
        try {
            if (empty($order_id)) {
                return response()->json([
                    "result" => false,
                    "message" => "Order ID is required.",
                ], 400);
            }

            // Fetch order with ordered items and product details
            $order = DB::table('orders')
                ->where('order_id', $order_id)
                ->select('order_id', 'user_id', 'total_amount', 'status', 'order_type', 'created_at', 'updated_at')
                ->first();

            if (!$order) {
                return response()->json([
                    "result" => false,
                    "message" => "Order not found.",
                ], 404);
            }

            // Fetch ordered items with product details
            $orderedItems = DB::table('ordered_items')
                ->join('products', 'ordered_items.product_id', '=', 'products.product_id')
                ->where('ordered_items.order_id', $order_id)
                ->select(
                    'ordered_items.ordered_item_id',
                    'ordered_items.order_id',
                    'ordered_items.product_id',
                    'products.product_name',
                    'products.product_description',
                    'ordered_items.quantity',
                    'ordered_items.price',
                    'ordered_items.total'
                )
                ->get();

            $order->ordered_items = $orderedItems;

            $data = [
                'order_id' => encrypt($order->order_id),
                'user_id' => encrypt($order->user_id),
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'order_type' => $order->order_type,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'ordered_items' => [
                    'items' => $orderedItems->map(function ($item) {
                        return [
                            'ordered_item_id' => encrypt($item->ordered_item_id),
                            'order_id' => $item->order_id,
                            'product_id' => encrypt($item->product_id), 
                            'product_name' => $item->product_name,
                            'product_description' => $item->product_description,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total' => $item->total,
                        ];
                    }),
                ],
            ];

            return response()->json([
                "result" => true,
                "order" => $order,
                "data" => $data,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                "result" => false,
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        return view('pos.show', compact('id'));
    }
    public function destroy($id)
    {
        // Logic to delete the POS entry
        return redirect()->route('pos.index')->with('success', 'POS entry deleted successfully.');
    }
}
