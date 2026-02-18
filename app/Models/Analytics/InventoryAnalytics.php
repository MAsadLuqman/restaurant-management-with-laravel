<?php

namespace App\Models\Analytics;

use App\Models\InventoryItem;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryAnalytics
{
    public static function getStockLevels()
    {
        return InventoryItem::where('is_active', true)
            ->selectRaw('*,
                                      (current_stock / minimum_stock) as stock_ratio,
                                      (current_stock * unit_cost) as total_value')
            ->get()
            ->groupBy(function ($item) {
                if ($item->current_stock <= 0) return 'out_of_stock';
                if ($item->current_stock <= $item->minimum_stock) return 'low_stock';
                if ($item->stock_ratio <= 2) return 'medium_stock';
                return 'high_stock';
            });
    }

    public static function getInventoryTurnover($startDate, $endDate)
    {
        return DB::table('inventory_items')
            ->join('menu_item_inventory', 'inventory_items.id', '=', 'menu_item_inventory.inventory_item_id')
            ->join('menu_items', 'menu_item_inventory.menu_item_id', '=', 'menu_items.id')
            ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->selectRaw('inventory_items.id,
                            inventory_items.name,
                            inventory_items.current_stock,
                            inventory_items.unit_cost,
                            SUM(order_items.quantity * menu_item_inventory.quantity_required) as total_consumed,
                            (SUM(order_items.quantity * menu_item_inventory.quantity_required) / inventory_items.current_stock) as turnover_ratio')
            ->groupBy('inventory_items.id', 'inventory_items.name', 'inventory_items.current_stock', 'inventory_items.unit_cost')
            ->orderBy('turnover_ratio', 'desc')
            ->get();
    }

    public static function getWastageAnalysis($startDate, $endDate)
    {
        // This would require a wastage tracking system
        // For now, we'll estimate based on expired items and overstock
        return InventoryItem::where('is_active', true)
            ->selectRaw('*,
                                      CASE
                                          WHEN current_stock > (minimum_stock * 5) THEN (current_stock - minimum_stock * 5) * unit_cost
                                          ELSE 0
                                      END as potential_wastage')
            ->having('potential_wastage', '>', 0)
            ->orderBy('potential_wastage', 'desc')
            ->get();
    }

    public static function getInventoryValue()
    {
        return [
            'total_value' => InventoryItem::where('is_active', true)
                    ->selectRaw('SUM(current_stock * unit_cost) as total')
                    ->value('total') ?? 0,
            'by_category' => DB::table('inventory_items')
                ->join('menu_item_inventory', 'inventory_items.id', '=', 'menu_item_inventory.inventory_item_id')
                ->join('menu_items', 'menu_item_inventory.menu_item_id', '=', 'menu_items.id')
                ->join('categories', 'menu_items.category_id', '=', 'categories.id')
                ->where('inventory_items.is_active', true)
                ->selectRaw('categories.name as category,
                                          SUM(inventory_items.current_stock * inventory_items.unit_cost) as value')
                ->groupBy('categories.id', 'categories.name')
                ->get()
        ];
    }

    public static function getForecastDemand($days = 30)
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('menu_item_inventory', 'menu_items.id', '=', 'menu_item_inventory.menu_item_id')
            ->join('inventory_items', 'menu_item_inventory.inventory_item_id', '=', 'inventory_items.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->selectRaw('inventory_items.id,
                                  inventory_items.name,
                                  inventory_items.current_stock,
                                  inventory_items.minimum_stock,
                                  AVG(order_items.quantity * menu_item_inventory.quantity_required) as avg_daily_consumption,
                                  (inventory_items.current_stock / AVG(order_items.quantity * menu_item_inventory.quantity_required)) as days_remaining')
            ->groupBy('inventory_items.id', 'inventory_items.name', 'inventory_items.current_stock', 'inventory_items.minimum_stock')
            ->having('days_remaining', '<', 7)
            ->orderBy('days_remaining')
            ->get();
    }
}
