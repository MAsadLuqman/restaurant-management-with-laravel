<?php

namespace App\Models\Analytics;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesAnalytics
{
    public static function getDailySales($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date,
                              SUM(total_amount) as revenue,
                              COUNT(*) as orders,
                              AVG(total_amount) as avg_order_value')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public static function getHourlySales($date = null)
    {
        $date = $date ?? today();

        return Order::whereDate('created_at', $date)
            ->where('status', 'completed')
            ->selectRaw('HOUR(created_at) as hour,
                              SUM(total_amount) as revenue,
                              COUNT(*) as orders')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    public static function getTopSellingItems($startDate, $endDate, $limit = 10)
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->selectRaw('menu_items.id,
                                  menu_items.name,
                                  categories.name as category_name,
                                  SUM(order_items.quantity) as total_quantity,
                                  SUM(order_items.total_price) as total_revenue,
                                  AVG(order_items.unit_price) as avg_price,
                                  COUNT(DISTINCT orders.id) as order_frequency')
            ->groupBy('menu_items.id', 'menu_items.name', 'categories.name')
            ->orderBy('total_quantity', 'desc')
            ->take($limit)
            ->get();
    }

    public static function getCustomerAnalytics($startDate, $endDate)
    {
        return [
            'new_customers' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('customer_phone')
                ->distinct('customer_phone')
                ->count(),
            'repeat_customers' => DB::table('orders')
                ->select('customer_phone')
                ->whereNotNull('customer_phone')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->groupBy('customer_phone')
                ->havingRaw('COUNT(*) > 1')
                ->count(),
            'avg_orders_per_customer' => DB::table('orders')
                    ->whereNotNull('customer_phone')
                    ->groupBy('customer_phone')
                    ->selectRaw('AVG(order_count) as avg_orders')
                    ->fromSub(function ($query) use ($startDate, $endDate) {
                        $query->from('orders')
                            ->selectRaw('customer_phone, COUNT(*) as order_count')
                            ->whereNotNull('customer_phone')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->where('status', 'completed')
                            ->groupBy('customer_phone');
                    }, 'customer_orders')
                    ->value('avg_orders') ?? 0
        ];
    }

    public static function getRevenueByOrderType($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('type,
                              SUM(total_amount) as revenue,
                              COUNT(*) as orders,
                              AVG(total_amount) as avg_order_value')
            ->groupBy('type')
            ->get();
    }

    public static function getPeakHours($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('HOUR(created_at) as hour,
                              COUNT(*) as order_count,
                              SUM(total_amount) as revenue')
            ->groupBy('hour')
            ->orderBy('order_count', 'desc')
            ->get();
    }

    public static function getSeasonalTrends($year = null)
    {
        $year = $year ?? date('Y');

        return Order::whereYear('created_at', $year)
            ->where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month,
                              MONTHNAME(created_at) as month_name,
                              SUM(total_amount) as revenue,
                              COUNT(*) as orders,
                              AVG(total_amount) as avg_order_value')
            ->groupBy('month', 'month_name')
            ->orderBy('month')
            ->get();
    }
}
