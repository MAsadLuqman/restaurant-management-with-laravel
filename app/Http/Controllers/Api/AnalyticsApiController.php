<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Analytics\SalesAnalytics;
use App\Models\Order;
use App\Models\Table;
use Carbon\Carbon;

class AnalyticsApiController extends Controller
{
    public function realtimeMetrics()
    {
        $today = today();

        return response()->json([
            'orders_today' => Order::whereDate('created_at', $today)->count(),
            'revenue_today' => Order::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('total_amount'),
            'avg_order_value' => Order::whereDate('created_at', $today)
                    ->where('status', 'completed')
                    ->avg('total_amount') ?? 0,
            'active_tables' => Table::where('status', 'occupied')->count(),
            'timestamp' => now()->toISOString()
        ]);
    }

    public function activeOrders()
    {
        $orders = Order::whereDate('created_at', today())
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->with(['table', 'orderItems'])
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'table' => $order->table,
                    'customer_name' => $order->customer_name,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'order_items_count' => $order->orderItems->count(),
                    'created_at_human' => $order->created_at->diffForHumans()
                ];
            });

        return response()->json($orders);
    }

    public function hourlySales($date = null)
    {
        $date = $date ? Carbon::parse($date) : today();
        $sales = SalesAnalytics::getHourlySales($date);

        return response()->json($sales);
    }

    public function dashboardData()
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        return response()->json([
            'sales_data' => SalesAnalytics::getDailySales($startDate, $endDate),
            'top_items' => SalesAnalytics::getTopSellingItems($startDate, $endDate, 10),
            'customer_analytics' => SalesAnalytics::getCustomerAnalytics($startDate, $endDate),
            'revenue_by_type' => SalesAnalytics::getRevenueByOrderType($startDate, $endDate),
            'peak_hours' => SalesAnalytics::getPeakHours($startDate, $endDate)
        ]);
    }

    public function salesForecast()
    {
        // Implementation would use machine learning models
        // For now, return mock forecast data
        $forecast = [];
        for ($i = 1; $i <= 7; $i++) {
            $forecast[] = [
                'date' => now()->addDays($i)->format('Y-m-d'),
                'predicted_revenue' => rand(5000, 15000),
                'confidence' => rand(75, 95)
            ];
        }

        return response()->json($forecast);
    }

    public function inventoryAlerts()
    {
        $lowStockItems = \App\Models\InventoryItem::whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'low_stock_count' => $lowStockItems->count(),
            'items' => $lowStockItems
        ]);
    }
}
