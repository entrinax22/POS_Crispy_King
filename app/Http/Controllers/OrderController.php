<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(){
        return Inertia::render('orders/Index');
    }

    public function list(Request $request)
    {
        $search = $request->input('search');

        $orders = Order::with(['user', 'orderItems.product'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('order_type', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('order_id')
            ->paginate(10);

        $data = $orders->getCollection()->map(function ($order) {
            return [
                'order_id' => encrypt($order->order_id),
                'customer_name' => $order->user->name ?? 'N/A',
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'order_type' => $order->order_type,
                'ordered_items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_id' => encrypt($item->product_id),
                        'product_name' => $item->product->product_name ?? 'Unknown',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->total,
                    ];
                }),
            ];
        });

        return response()->json([
            'result' => true,
            'message' => 'Orders retrieved successfully.',
            'data' => $data,
            'pagination' => [
                'total' => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
            ],
        ]);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
            'order_type' => 'required|string',
            'status' => 'required|string',
            'ordered_items' => 'required|array|min:1',
            'ordered_items.*.product_id' => 'required|string',
            'ordered_items.*.quantity' => 'required|numeric|min:1',
            'ordered_items.*.price' => 'required|numeric|min:0',
            'ordered_items.*.total' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $orderId = decrypt($validated['order_id']);
            $order = Order::with('orderItems')->findOrFail($orderId);

            $order->update([
                'order_type' => $validated['order_type'],
                'status' => $validated['status'],
                'total_amount' => $validated['total_amount'],
            ]);

            $order->orderItems()->delete();

            foreach ($validated['ordered_items'] as $item) {
                OrderedItem::create([
                    'order_id' => $order->order_id, 
                    'product_id' => decrypt($item['product_id']),
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            $order->load('orderItems');

            return response()->json([
                'result' => true,
                'message' => 'Order updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Failed to update order: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function edit($encryptedId){
        $id = decrypt($encryptedId);
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        $products = Product::query()
            ->orderBy('product_name')
            ->get()
            ->map(function ($product) {
                return [
                    'product_id' => encrypt($product->product_id),
                    'product_name' => $product->product_name,
                    'product_price' => (float) $product->product_price,
                ];
            });

        $data = [
            'order_id' => encrypt($order->order_id),
            'total_amount' => (float) $order->total_amount,
            'status' => $order->status,
            'order_type' => $order->order_type,
            'ordered_items' => $order->orderItems->map(function ($item) {
                return [
                    'product_id' => encrypt($item->product_id),
                    'product_name' => $item->product->product_name ?? 'Unknown',
                    'quantity' => (int)$item->quantity,
                    'price' => (float) $item->price,
                    'total' => (float) $item->total,
                ];
            }),
            'products' => $products,
        ];

        return response()->json([
            'result' => true,
            'data' => $data,
            'message' => 'Order retrieved successfully.',
        ]);
    }

    public function destroy($encryptedId){
        $id = decrypt($encryptedId);
        $order = Order::findOrFail($id);

        try {
            DB::beginTransaction();
            $order->orderItems()->delete();
            $order->delete();
            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Order deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'result' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
