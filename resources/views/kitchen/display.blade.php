@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Kitchen Display System</h2>
                    <div class="badge bg-primary fs-6">
                        <span id="currentTime"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="ordersContainer">
            @foreach($orders as $order)
                <div class="col-md-4 mb-4 order-card" data-order-id="{{ $order->id }}">
                    <div class="card border-{{ $order->status === 'confirmed' ? 'warning' : 'info' }}">
                        <div class="card-header bg-{{ $order->status === 'confirmed' ? 'warning' : 'info' }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $order->order_number }}</h5>
                                <span class="badge bg-light text-dark">
                                {{ ucfirst(str_replace('_', ' ', $order->type)) }}
                            </span>
                            </div>
                            <small>
                                @if($order->table)
                                    Table {{ $order->table->table_number }}
                                @else
                                    {{ $order->customer_name }}
                                @endif
                            </small>
                        </div>
                        <div class="card-body">
                            <div class="order-items mb-3">
                                @foreach($order->orderItems as $item)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ $item->menuItem->name }}</span>
                                        <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                    </div>
                                    @if($item->special_instructions)
                                        <small class="text-muted">Note: {{ $item->special_instructions }}</small>
                                    @endif
                                @endforeach
                            </div>

                            @if($order->special_instructions)
                                <div class="alert alert-info py-2">
                                    <small><strong>Special Instructions:</strong> {{ $order->special_instructions }}</small>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    {{ $order->created_at->diffForHumans() }}
                                </small>
                                <div>
                                    @if($order->status === 'confirmed')
                                        <button class="btn btn-sm btn-primary update-status" data-status="preparing">
                                            Start Preparing
                                        </button>
                                    @elseif($order->status === 'preparing')
                                        <button class="btn btn-sm btn-success update-status" data-status="ready">
                                            Mark Ready
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Update current time
            function updateTime() {
                const now = new Date();
                $('#currentTime').text(now.toLocaleTimeString());
            }
            updateTime();
            setInterval(updateTime, 1000);

            // Update order status
            $('.update-status').click(function() {
                const button = $(this);
                const orderId = button.closest('.order-card').data('order-id');
                const status = button.data('status');

                $.ajax({
                    url: `/orders/${orderId}/status`,
                    method: 'PATCH',
                    data: {
                        status: status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove card if order is ready (moved to serving area)
                            if (status === 'ready') {
                                button.closest('.order-card').fadeOut();
                            } else {
                                // Update button for next status
                                button.removeClass('btn-primary').addClass('btn-success');
                                button.text('Mark Ready');
                                button.data('status', 'ready');
                            }
                        }
                    },
                    error: function() {
                        alert('Error updating order status');
                    }
                });
            });

            // Real-time updates using Pusher/Laravel Echo
            window.Echo.channel('kitchen-display')
                .listen('OrderStatusUpdated', (e) => {
                    // Add new orders or update existing ones
                    if (e.order.status === 'confirmed' || e.order.status === 'preparing') {
                        updateOrderDisplay(e.order);
                    } else {
                        // Remove completed orders
                        $(`.order-card[data-order-id="${e.order.id}"]`).fadeOut();
                    }
                });

            function updateOrderDisplay(order) {
                // Implementation for updating order display in real-time
                // This would rebuild the order card with new data
                console.log('Order updated:', order);
            }
        });
    </script>
@endsection
