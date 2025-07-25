<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use Illuminate\Http\Request;

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



    public function store(Request $request){
        // Logic to store order
    }

    public function edit($encryptedId){
        // Logic to edit order
    }

    public function destroy($encryptedId){
        // Logic to delete order
    }
}
