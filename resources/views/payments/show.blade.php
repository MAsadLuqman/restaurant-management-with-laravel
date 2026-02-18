@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4>Payment Receipt #{{ $payment->id }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Order Number:</strong>
                            <a href="{{ route('orders.show', $payment->order) }}">{{ $payment->order->order_number }}</a>
                        </div>
                        <div class="mb-3">
                            <strong>Amount Paid:</strong> ₹{{ number_format($payment->amount, 2) }}
                        </div>
                        <div class="mb-3">
                            <strong>Payment Method:</strong> <span class="badge bg-secondary">{{ strtoupper($payment->payment_method) }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                        </div>
                        <div class="mb-3">
                            <strong>Transaction ID:</strong> {{ $payment->transaction_id ?? 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <strong>Payment Date:</strong> {{ $payment->created_at->format('M d, Y h:i A') }}
                        </div>
                        <div class="mb-3">
                            <strong>Notes:</strong> {{ $payment->notes ?? 'None' }}
                        </div>

                        <hr>

                        <h5>Order Items</h5>
                        <ul class="list-group mb-3">
                            @foreach($payment->order->orderItems as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        {{ $item->menuItem->name }} x {{ $item->quantity }}
                                        <br><small class="text-muted">@ ₹{{ number_format($item->unit_price, 2) }} each</small>
                                    </div>
                                    <span>₹{{ number_format($item->total_price, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="d-flex justify-content-between mb-2">
                            <strong>Subtotal:</strong> <span>₹{{ number_format($payment->order->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Tax:</strong> <span>₹{{ number_format($payment->order->tax_amount, 2) }}</span>
                        </div>
                        @if($payment->order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Discount:</strong> <span>- ₹{{ number_format($payment->order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <strong>Total:</strong> <span>₹{{ number_format($payment->order->total_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back to Payments</a>
                            <button class="btn btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
