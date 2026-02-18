@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4">Dashboard</h1>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $todayOrders ?? 0 }}</h4>
                                <p class="mb-0">Today's Orders</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>₹{{ number_format($todayRevenue ?? 0, 2) }}</h4>
                                <p class="mb-0">Today's Revenue</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-rupee-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $occupiedTables ?? 0 }}</h4>
                                <p class="mb-0">Occupied Tables</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-table fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $pendingOrders ?? 0 }}</h4>
                                <p class="mb-0">Pending Orders</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Type</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recentOrders ?? [] as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $order->type)) }}
                                        </span>
                                        </td>
                                        <td>
                                            @if($order->table)
                                                Table {{ $order->table->table_number }}
                                            @else
                                                {{ $order->customer_name }}
                                            @endif
                                        </td>
                                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        </td>
                                        <td>{{ $order->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No recent orders</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Alerts -->
            <div class="col-md-4">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>New Order
                            </a>
                            <a href="{{ route('reservations.create') }}" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>New Reservation
                            </a>
                            <a href="{{ route('kitchen.display') }}" class="btn btn-warning">
                                <i class="fas fa-fire me-2"></i>Kitchen Display
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                @if(isset($lowStockItems) && $lowStockItems->count() > 0)
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5>Low Stock Alert</h5>
                        </div>
                        <div class="card-body">
                            @foreach($lowStockItems as $item)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $item->name }}</span>
                                    <span class="badge bg-danger">{{ $item->current_stock }} {{ $item->unit }}</span>
                                </div>
                            @endforeach
                            <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Real-time updates for dashboard
            window.Echo.channel('orders')
                .listen('OrderStatusUpdated', (e) => {
                    // Refresh dashboard stats
                    location.reload();
                });

            // Auto-refresh dashboard every 30 seconds
            setInterval(function() {
                location.reload();
            }, 30000);
        });
    </script>
@endsection
