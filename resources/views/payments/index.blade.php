@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Payments</h1>
                    <div>
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>₹{{ number_format($payments->where('status', 'completed')->sum('amount'), 2) }}</h3>
                        <p class="mb-0">Total Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>₹{{ number_format($payments->where('status', 'pending')->sum('amount'), 2) }}</h3>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ $payments->where('payment_method', 'cash')->count() }}</h3>
                        <p class="mb-0">Cash Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $payments->whereIn('payment_method', ['card', 'upi'])->count() }}</h3>
                        <p class="mb-0">Digital Payments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Payment History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Order</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>#{{ $payment->id }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $payment->order) }}">
                                                {{ $payment->order->order_number }}
                                            </a>
                                        </td>
                                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                        <span class="badge bg-secondary">
                                            {{ strtoupper($payment->payment_method) }}
                                        </span>
                                        </td>
                                        <td>
                                        <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        </td>
                                        <td>{{ $payment->transaction_id ?? '-' }}</td>
                                        <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">
                                                View Receipt
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No payments found</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
@endsection
