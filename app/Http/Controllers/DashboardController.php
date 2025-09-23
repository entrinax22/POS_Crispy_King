<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Stats ---
        $totalSales = Order::where('status', 'Completed')->sum('total_amount');
        $ordersToday = Order::whereDate('created_at', Carbon::today())->count();
        $customers = User::count();

        $stats = [
            'totalSales' => $totalSales,
            'ordersToday' => $ordersToday,
            'customers' => $customers,
        ];

        // --- Top Products ---
        $topProducts = OrderedItem::selectRaw('product_id, SUM(quantity) as total_sold')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->product_name,
                    'sold' => $item->total_sold,
                ];
            });

        // --- Recent Orders ---
        $recentOrders = Order::latest()
            ->take(5)
            ->get(['order_id', 'total_amount', 'created_at'])
            ->map(function ($order) {
                return [
                    'id' => $order->order_id,
                    'customer' => $order->user?->name ?? 'Guest',
                    'total' => '₱' . number_format($order->total_amount, 2),
                    'time' => $order->created_at->format('h:i A'),
                ];
            });

        // --- Sales Trend (last 7 days) ---
        $salesTrend = Order::where('status', 'Completed')
            ->whereBetween('created_at', [Carbon::now()->subDays(6), Carbon::now()])
            ->get()
            ->groupBy(function ($order) {
                return Carbon::parse($order->created_at)->format('D');
            })
            ->map(fn ($day) => $day->sum('total_amount'));

        // Ensure all days of the week exist
        $labels = collect(range(0, 6))->map(fn ($i) => Carbon::now()->subDays(6 - $i)->format('D'));
        $salesTrendData = $labels->map(fn ($day) => $salesTrend[$day] ?? 0);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'salesTrend' => [
                'labels' => $labels,
                'data' => $salesTrendData,
            ],
        ]);
    }

    public function generateReport()
    {
        try {
            // Today’s Orders
            $todayOrders = Order::whereDate('created_at', today())
                ->with('orderItems.product')
                ->get();

            // Total orders today
            $totalOrdersToday = $todayOrders->count();

            // Sales for today
            $salesToday = $todayOrders->sum('total_amount'); // adjust if your column name differs

            // Sales for the month
            $salesMonth = Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');

            // Orders count for the month
            $ordersMonth = Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // Popular product of the month
            $popularProduct = DB::table('ordered_items')
                ->join('products', 'ordered_items.product_id', '=', 'products.product_id')
                ->select(
                    'products.product_id',
                    'products.product_name',
                    DB::raw('SUM(ordered_items.quantity) as total_qty')
                )
                ->whereMonth('ordered_items.created_at', now()->month)
                ->whereYear('ordered_items.created_at', now()->year)
                ->groupBy('products.product_id', 'products.product_name')
                ->orderByDesc('total_qty')
                ->first();

            $popularProductDetails = null;
            if ($popularProduct) {
                $popularProductDetails = Product::find($popularProduct->product_id);
            }

            return response()->json([
                "result" => true,
                "data" => [
                    "date" => now()->toDateString(),
                    "total_orders_today" => $totalOrdersToday,
                    "sales_today" => $salesToday,
                    "orders_this_month" => $ordersMonth,
                    "sales_this_month" => $salesMonth,
                    "popular_product_this_month" => $popularProductDetails ? [
                        "product_name" => $popularProductDetails->product_name,
                        "total_qty" => $popularProduct->total_qty,
                    ] : null,
                    "today_orders_list" => $todayOrders,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "result" => false,
                "message" => $e->getMessage(),
            ], 500);
        }
    }

}
