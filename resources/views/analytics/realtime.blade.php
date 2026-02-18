@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Real-time Analytics</h1>
                    <div class="d-flex align-items-center">
                    <span class="badge bg-success me-3">
                        <i class="fas fa-circle me-1"></i>Live
                    </span>
                        <span id="lastUpdate" class="text-muted small"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 id="ordersToday">{{ $liveMetrics['orders_today'] }}</h4>
                                <p class="mb-0">Orders Today</p>
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
                                <h4 id="revenueToday">₹{{ number_format($liveMetrics['revenue_today'], 0) }}</h4>
                                <p class="mb-0">Revenue Today</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-rupee-sign fa-2x"></i>
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
                                <h4 id="avgOrderValue">₹{{ number_format($liveMetrics['avg_order_value'], 0) }}</h4>
                                <p class="mb-0">Avg Order Value</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x"></i>
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
                                <h4 id="activeTables">{{ $liveMetrics['active_tables'] }}</h4>
                                <p class="mb-0">Active Tables</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-table fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Hourly Sales Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Today's Hourly Sales</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="hourlySalesChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Current Orders Status -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Current Orders Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Orders -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Active Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="activeOrdersTable">
                                <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Table</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($currentOrders as $order)
                                    <tr data-order-id="{{ $order->id }}">
                                        <td>{{ $order->order_number }}</td>
                                        <td>
                                            @if($order->table)
                                                Table {{ $order->table->table_number }}
                                            @else
                                                {{ $order->customer_name }}
                                            @endif
                                        </td>
                                        <td>{{ $order->orderItems->count() }} items</td>
                                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                        <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'preparing' ? 'info' : 'primary') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        </td>
                                        <td>{{ $order->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Indicators -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Performance Indicators</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 85%">85%</div>
                                    </div>
                                    <small>Order Accuracy</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 92%">92%</div>
                                    </div>
                                    <small>Customer Satisfaction</small>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 78%">78%</div>
                                    </div>
                                    <small>Kitchen Efficiency</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 88%">88%</div>
                                    </div>
                                    <small>Table Turnover</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Feed -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Activity</h5>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        <div id="activityFeed">
                            <!-- Activity items will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let hourlySalesChart;
        let orderStatusChart;

        // Initialize charts
        function initializeCharts() {
            // Hourly Sales Chart
            const hourlySalesCtx = document.getElementById('hourlySalesChart').getContext('2d');
            hourlySalesChart = new Chart(hourlySalesCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($todaySales->pluck('hour')->map(function($hour) { return $hour . ':00'; })) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($todaySales->pluck('revenue')) !!},
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Order Status Chart
            const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
            const statusCounts = {
                pending: {{ $currentOrders->where('status', 'pending')->count() }},
                confirmed: {{ $currentOrders->where('status', 'confirmed')->count() }},
                preparing: {{ $currentOrders->where('status', 'preparing')->count() }}
            };

            orderStatusChart = new Chart(orderStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Confirmed', 'Preparing'],
                    datasets: [{
                        data: [statusCounts.pending, statusCounts.confirmed, statusCounts.preparing],
                        backgroundColor: [
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(23, 162, 184, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Real-time updates
        function updateMetrics() {
            fetch('/api/realtime-metrics')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('ordersToday').textContent = data.orders_today;
                    document.getElementById('revenueToday').textContent = '₹' + data.revenue_today.toLocaleString();
                    document.getElementById('avgOrderValue').textContent = '₹' + Math.round(data.avg_order_value);
                    document.getElementById('activeTables').textContent = data.active_tables;

                    // Update last update time
                    document.getElementById('lastUpdate').textContent = 'Last updated: ' + new Date().toLocaleTimeString();
                })
                .catch(error => console.error('Error updating metrics:', error));
        }

        // Update active orders table
        function updateActiveOrders() {
            fetch('/api/active-orders')
                .then(response => response.json())
                .then(orders => {
                    const tbody = document.querySelector('#activeOrdersTable tbody');
                    tbody.innerHTML = '';

                    orders.forEach(order => {
                        const statusClass = order.status === 'pending' ? 'warning' :
                            order.status === 'preparing' ? 'info' : 'primary';

                        const row = `
                    <tr data-order-id="${order.id}">
                        <td>${order.order_number}</td>
                        <td>${order.table ? 'Table ' + order.table.table_number : order.customer_name}</td>
                        <td>${order.order_items_count} items</td>
                        <td>₹${order.total_amount.toLocaleString()}</td>
                        <td><span class="badge bg-${statusClass}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span></td>
                        <td>${order.created_at_human}</td>
                    </tr>
                `;
                        tbody.innerHTML += row;
                    });
                })
                .catch(error => console.error('Error updating orders:', error));
        }

        // Add activity to feed
        function addActivity(activity) {
            const feed = document.getElementById('activityFeed');
            const activityItem = document.createElement('div');
            activityItem.className = 'activity-item mb-2 p-2 border-start border-3 border-primary';
            activityItem.innerHTML = `
        <small class="text-muted">${new Date().toLocaleTimeString()}</small>
        <div>${activity}</div>
    `;
            feed.insertBefore(activityItem, feed.firstChild);

            // Keep only last 10 activities
            while (feed.children.length > 10) {
                feed.removeChild(feed.lastChild);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();

            // Update metrics every 30 seconds
            setInterval(updateMetrics, 30000);

            // Update active orders every 15 seconds
            setInterval(updateActiveOrders, 15000);

            // Set initial last update time
            document.getElementById('lastUpdate').textContent = 'Last updated: ' + new Date().toLocaleTimeString();
        });

        // WebSocket connection for real-time updates
        if (window.Echo) {
            window.Echo.channel('orders')
                .listen('OrderStatusUpdated', (e) => {
                    addActivity(`Order ${e.order.order_number} status changed to ${e.order.status}`);
                    updateMetrics();
                    updateActiveOrders();
                });

            window.Echo.channel('kitchen-display')
                .listen('OrderStatusUpdated', (e) => {
                    // Update charts if needed
                    if (hourlySalesChart && e.order.status === 'completed') {
                        // Refresh hourly sales data
                        updateMetrics();
                    }
                });
        }
    </script>

    <style>
        .activity-item {
            transition: all 0.3s ease;
        }

        .activity-item:first-child {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .badge {
            font-size: 0.75em;
        }

        .progress {
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
        }
    </style>
@endsection
