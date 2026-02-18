<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create(Order $order)
    {
        if ($order->payments()->where('status', 'completed')->exists()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Order is already paid');
        }

        return view('payments.create', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cash,card,upi,wallet',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $order = Order::find($request->order_id);

        // Check if payment amount matches order total
        if ($request->amount != $order->total_amount) {
            return back()->withErrors(['amount' => 'Payment amount must match order total']);
        }

        $payment = Payment::create([
            'order_id' => $request->order_id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'status' => 'completed', // For cash payments, mark as completed immediately
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes,
        ]);

        // Update order status
        $order->update(['status' => 'completed']);

        // Free up table if dine-in
        if ($order->table && $order->type === 'dine_in') {
            $order->table->update(['status' => 'available']);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment processed successfully');
    }

    public function show(Payment $payment)
    {
        $payment->load('order.orderItems.menuItem');
        return view('payments.show', compact('payment'));
    }

    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,upi,wallet',
            'transaction_id' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => $request->payment_method,
            'amount' => $order->total_amount,
            'status' => 'completed',
            'transaction_id' => $request->transaction_id,
        ]);

        $order->update(['status' => 'completed']);

        if ($order->table && $order->type === 'dine_in') {
            $order->table->update(['status' => 'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'payment' => $payment
        ]);
    }
}
