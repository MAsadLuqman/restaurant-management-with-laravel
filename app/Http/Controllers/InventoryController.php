<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $items = InventoryItem::where('is_active', true)
            ->orderBy('name')
            ->get();

        $lowStockItems = $items->filter(function ($item) {
            return $item->current_stock <= $item->minimum_stock;
        });

        return view('inventory.index', compact('items', 'lowStockItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $item = InventoryItem::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Inventory item created successfully',
            'item' => $item
        ]);
    }

    public function updateStock(Request $request, InventoryItem $item)
    {
        $request->validate([
            'quantity' => 'required|numeric',
            'type' => 'required|in:add,subtract',
            'notes' => 'nullable|string'
        ]);

        $newStock = $request->type === 'add'
            ? $item->current_stock + $request->quantity
            : $item->current_stock - $request->quantity;

        if ($newStock < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock'
            ], 400);
        }

        $item->update(['current_stock' => $newStock]);

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'new_stock' => $newStock
        ]);
    }

    public function lowStockAlert()
    {
        $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'items' => $lowStockItems,
            'count' => $lowStockItems->count()
        ]);
    }
}
