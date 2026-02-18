<?php

namespace App\Models\Analytics;

use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StaffAnalytics
{
    public static function getStaffPerformance($startDate, $endDate)
    {
        return User::join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->selectRaw('users.id,
                              users.name,
                              COUNT(orders.id) as total_orders,
                              SUM(orders.total_amount) as total_sales,
                              AVG(orders.total_amount) as avg_order_value,
                              AVG(TIMESTAMPDIFF(MINUTE, orders.created_at, orders.updated_at)) as avg_processing_time')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_sales', 'desc')
            ->get();
    }

    public static function getShiftAnalysis($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('CASE
                              WHEN HOUR(created_at) BETWEEN 6 AND 14 THEN "Morning"
                              WHEN HOUR(created_at) BETWEEN 15 AND 22 THEN "Evening"
                              ELSE "Night"
                              END as shift,
                              COUNT(*) as orders,
                              SUM(total_amount) as revenue,
                              AVG(total_amount) as avg_order_value')
            ->groupBy('shift')
            ->get();
    }

    public static function getProductivityMetrics($userId, $startDate, $endDate)
    {
        $orders = Order::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        return [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'avg_order_value' => $orders->avg('total_amount'),
            'orders_per_day' => $orders->count() / max(1, $startDate->diffInDays($endDate)),
            'peak_hour' => $orders->groupBy(function ($order) {
                return $order->created_at->format('H');
            })->sortByDesc(function ($hourOrders) {
                return $hourOrders->count();
            })->keys()->first()
        ];
    }

    public static function getEfficiencyMetrics($startDate, $endDate)
    {
        return [
            'avg_order_preparation_time' => Order::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_time')
                    ->value('avg_time') ?? 0,
            'order_accuracy' => self::calculateOrderAccuracy($startDate, $endDate),
            'table_turnover' => self::calculateTableTurnover($startDate, $endDate)
        ];
    }

    private static function calculateOrderAccuracy($startDate, $endDate)
    {
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'cancelled'])
            ->count();

        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        return $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
    }

    private static function calculateTableTurnover($startDate, $endDate)
    {
        return DB::table('orders')
            ->join('tables', 'orders.table_id', '=', 'tables.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->where('orders.type', 'dine_in')
            ->selectRaw('tables.id,
                            tables.table_number,
                            COUNT(orders.id) as total_orders,
                            AVG(TIMESTAMPDIFF(MINUTE, orders.created_at, orders.updated_at)) as avg_dining_time')
            ->groupBy('tables.id', 'tables.table_number')
            ->orderBy('total_orders', 'desc')
            ->get();
    }
}
