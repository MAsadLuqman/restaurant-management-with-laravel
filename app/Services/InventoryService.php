<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function deductInventoryForMenuItem(MenuItem $menuItem, int $quantity)
    {
        // Get inventory items required for this menu item
        $inventoryItems = $menuItem->inventoryItems;

        foreach ($inventoryItems as $inventoryItem) {
            $requiredQuantity = $inventoryItem->pivot->quantity_required * $quantity;

            if ($inventoryItem->current_stock < $requiredQuantity) {
                throw new \Exception("Insufficient stock for {$inventoryItem->name}. Required: {$requiredQuantity}, Available: {$inventoryItem->current_stock}");
            }

            $inventoryItem->decrement('current_stock', $requiredQuantity);

            Log::info('Inventory deducted', [
                'item' => $inventoryItem->name,
                'quantity_deducted' => $requiredQuantity,
                'remaining_stock' => $inventoryItem->fresh()->current_stock
            ]);
        }
    }

    public function checkLowStock()
    {
        return InventoryItem::whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->get();
    }

    public function generateStockReport()
    {
        $items = InventoryItem::where('is_active', true)->get();

        return [
            'total_items' => $items->count(),
            'low_stock_items' => $items->filter(fn($item) => $item->current_stock <= $item->minimum_stock)->count(),
            'total_value' => $items->sum(fn($item) => $item->current_stock * $item->unit_cost),
            'items' => $items
        ];
    }
}
