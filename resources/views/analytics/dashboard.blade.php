@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Analytics Dashboard</h1>
                    <div class="btn-group" role="group">
                        <a href="{{ route('analytics.sales') }}" class="btn btn-outline-primary">Sales Analytics</a>
                        <a href="{{ route('analytics.inventory') }}" class="btn btn-outline-success">Inventory Analytics</a>
                        <a href="{{ route('analytics.staff') }}" class="btn btn-outline-info">Staff Analytics</a>
                        <a href="{{ route('analytics.predictive') }}" class="btn btn-outline-warning">Predictive Analytics</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>₹{{ number_format($salesData->sum('revenue'), 2) }}</h4>
                                <p class="mb-0">Total Revenue</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-rupee-sign fa-2x"></i>
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
                                <h4>{{ $salesData->sum('orders') }}</h4>
                                <p class="mb-0">Total Orders</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x"></i>
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
                                <h4>₹{{ number_format($salesData->avg('avg_order_value'), 2) }}</h4>
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
                                <h4>{{ $customerAnalytics['new_customers'] }}</h4>
                                <p class="mb-0">New Customers</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sales Trend Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Sales Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesTrendChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Revenue by Order Type -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Revenue by Order Type</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="orderTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Top Selling Items -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Top Selling Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Revenue</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topItems as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->total_quantity }}</td>
                                        <td>₹{{ number_format($item->total_revenue, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peak Hours -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Peak Hours Analysis</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="peakHoursChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Analytics -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Customer Analytics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h3 class="text-primary">{{ $customerAnalytics['new_customers'] }}</h3>
                                    <p>New Customers</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h3 class="text-success">{{ $customerAnalytics['repeat_customers'] }}</h3>
                                    <p>Repeat Customers</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h3 class="text-info">{{ number_format($customerAnalytics['avg_orders_per_customer'], 1) }}</h3>
                                    <p>Avg Orders per Customer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($salesData->pluck('date')) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($salesData->pluck('revenue')) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Orders',
                    data: {!! json_encode($salesData->pluck('orders')) !!},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    yAxisID: 'y1',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });

        // Order Type Chart
        const orderTypeCtx = document.getElementById('orderTypeChart').getContext('2d');
        new Chart(orderTypeCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($revenueByType->pluck('type')->map(function($type) { return ucfirst(str_replace('_', ' ', $type)); })) !!},
                datasets: [{
                    data: {!! json_encode($revenueByType->pluck('revenue')) !!},
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)'
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

        // Peak Hours Chart
        const peakHoursCtx = document.getElementById('peakHoursChart').getContext('2d');
        new Chart(peakHoursCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($peakHours->pluck('hour')->map(function($hour) { return $hour . ':00'; })) !!},
                datasets: [{
                    label: 'Orders',
                    data: {!! json_encode($peakHours->pluck('order_count')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.8)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
