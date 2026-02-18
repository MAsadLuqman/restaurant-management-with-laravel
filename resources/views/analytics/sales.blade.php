@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Sales Analytics</h1>
                    <div class="btn-group" role="group">
                        <a href="{{ route('analytics.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                        <a href="{{ route('analytics.inventory') }}" class="btn btn-outline-success">Inventory Analytics</a>
                        <a href="{{ route('analytics.staff') }}" class="btn btn-outline-info">Staff Analytics</a>
                        <a href="{{ route('analytics.predictive') }}" class="btn btn-outline-warning">Predictive Analytics</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form method="GET">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Sales Metrics -->
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
                <div class="card bg-{{ $growthRate >= 0 ? 'success' : 'danger' }} text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ number_format($growthRate, 2) }}%</h4>
                                <p class="mb-0">Revenue Growth</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-{{ $growthRate >= 0 ? 'arrow-up' : 'arrow-down' }} fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Daily Sales Trend Chart -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Daily Sales Trend ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailySalesTrendChart" height="100"></canvas>
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
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Revenue</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topItems as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->category_name }}</td>
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

        <!-- Seasonal Trends & Customer Analytics -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Seasonal Sales Trends (Current Year)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="seasonalTrendsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
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
        // Daily Sales Trend Chart
        const dailySalesCtx = document.getElementById('dailySalesTrendChart').getContext('2d');
        new Chart(dailySalesCtx, {
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

        // Seasonal Trends Chart
        const seasonalTrendsCtx = document.getElementById('seasonalTrendsChart').getContext('2d');
        new Chart(seasonalTrendsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($seasonalTrends->pluck('month_name')) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($seasonalTrends->pluck('revenue')) !!},
                    borderColor: 'rgb(153, 102, 255)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
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
    </script>
@endsection
