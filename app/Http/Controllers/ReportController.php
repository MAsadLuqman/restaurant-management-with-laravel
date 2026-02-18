<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Daily sales
        $dailySales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling items
        $topItems = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->selectRaw('menu_items.name, SUM(order_items.quantity) as total_quantity, SUM(order_items.total_price) as total_revenue')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderBy('total_quantity', 'desc')
            ->take(10)
            ->get();

        // Payment methods
        $paymentMethods = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('payments.status', 'completed')
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        // Summary
        $summary = [
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('total_amount'),
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count(),
            'average_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->avg('total_amount'),
        ];

        return view('reports.sales', compact('dailySales', 'topItems', 'paymentMethods', 'summary', 'startDate', 'endDate'));
    }

    public function inventory()
    {
        $items = InventoryItem::where('is_active', true)->get();

        $summary = [
            'total_items' => $items->count(),
            'low_stock_items' => $items->filter(fn($item) => $item->isLowStock())->count(),
            'total_value' => $items->sum('total_value'),
            'out_of_stock' => $items->filter(fn($item) => $item->current_stock <= 0)->count(),
        ];

        $lowStockItems = $items->filter(fn($item) => $item->isLowStock());

        return view('reports.inventory', compact('items', 'summary', 'lowStockItems'));
    }

    public function staff(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $staffPerformance = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->select('users.name',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_sales'),
                DB::raw('AVG(orders.total_amount) as avg_order_value'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_sales', 'desc')
            ->get();

        return view('reports.staff', compact('staffPerformance', 'startDate', 'endDate'));
    }
}
