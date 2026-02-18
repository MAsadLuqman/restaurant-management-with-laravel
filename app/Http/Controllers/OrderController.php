<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Table;
use App\Services\WhatsAppService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\OrderStatusUpdated;

class OrderController extends Controller
{
    protected $whatsAppService;
    protected $inventoryService;

    public function __construct(WhatsAppService $whatsAppService, InventoryService $inventoryService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $orders = Order::with(['table', 'user', 'orderItems.menuItem'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $tables = Table::where('status', 'available')->get();
        $menuItems = MenuItem::with('category')->where('is_available', true)->get();

        return view('orders.create', compact('tables', 'menuItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:dine_in,takeaway,delivery',
            'table_id' => 'nullable|exists:tables,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'type' => $request->type,
                'table_id' => $request->table_id,
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'special_instructions' => $request->special_instructions,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
            ]);

            // Create order items
            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->price,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);

                // Deduct inventory
                $this->inventoryService->deductInventoryForMenuItem($menuItem, $item['quantity']);
            }

            // Calculate totals
            $order->calculateTotal();

            // Update table status if dine-in
            if ($request->type === 'dine_in' && $request->table_id) {
                Table::find($request->table_id)->update(['status' => 'occupied']);
            }

            // Send WhatsApp notification
            if ($request->customer_phone) {
                $this->whatsAppService->sendOrderConfirmation($order);
            }

            // Broadcast to kitchen
            broadcast(new OrderStatusUpdated($order))->toOthers();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => $order->load('orderItems.menuItem')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Order $order)
    {
        $order->load(['table', 'user', 'orderItems.menuItem']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,served,completed,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        // Send WhatsApp update
        if ($order->customer_phone) {
            $this->whatsAppService->sendOrderStatusUpdate($order);
        }

        // Broadcast update
        broadcast(new OrderStatusUpdated($order))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }

    public function kitchenDisplay()
    {
        $orders = Order::with(['orderItems.menuItem', 'table'])
            ->whereIn('status', ['confirmed', 'preparing'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('kitchen.display', compact('orders'));
    }
}
