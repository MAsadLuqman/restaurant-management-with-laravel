@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Predictive Analytics</h1>
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary" onclick="refreshForecasts()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Forecasts
                        </button>
                        <button class="btn btn-outline-success" onclick="exportForecasts()">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forecast Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ array_sum(array_column($demandForecast, 'predicted_orders')) }}</h3>
                        <p class="mb-0">Predicted Orders (7 days)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>₹{{ number_format(array_sum(array_column($salesForecast, 'predicted_revenue')), 0) }}</h3>
                        <p class="mb-0">Predicted Revenue (30 days)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>{{ count($inventoryRecommendations) }}</h3>
                        <p class="mb-0">Items Need Restocking</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ count($staffingRecommendations) }}</h3>
                        <p class="mb-0">Peak Hours Identified</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Demand Forecast -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>7-Day Demand Forecast</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="demandForecastChart" height="200"></canvas>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Forecast based on historical order patterns and trends
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Forecast -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>30-Day Revenue Forecast</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesForecastChart" height="200"></canvas>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Revenue projection using linear regression analysis
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Recommendations -->
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Inventory Restocking Recommendations</h5>
                    </div>
                    <div class="card-body">
                        @if(count($inventoryRecommendations) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Current Stock</th>
                                        <th>Daily Consumption</th>
                                        <th>Days Remaining</th>
                                        <th>Priority</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($inventoryRecommendations as $item)
                                        <tr class="{{ $item->days_remaining < 3 ? 'table-danger' : ($item->days_remaining < 5 ? 'table-warning' : '') }}">
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->current_stock }}</td>
                                            <td>{{ number_format($item->avg_daily_consumption, 2) }}</td>
                                            <td>{{ number_format($item->days_remaining, 1) }} days</td>
                                            <td>
                                                @if($item->days_remaining < 3)
                                                    <span class="badge bg-danger">Critical</span>
                                                @elseif($item->days_remaining < 5)
                                                    <span class="badge bg-warning">High</span>
                                                @else
                                                    <span class="badge bg-info">Medium</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="createPurchaseOrder({{ $item->id }})">
                                                    Order Now
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5>All inventory levels are optimal</h5>
                                <p class="text-muted">No immediate restocking required</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Staffing Recommendations -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Staffing Recommendations</h5>
                    </div>
                    <div class="card-body">
                        @if(count($staffingRecommendations) > 0)
                            @foreach($staffingRecommendations as $recommendation)
                                <div class="alert alert-info">
                                    <strong>{{ $recommendation['hour'] }}:00 - {{ $recommendation['hour'] + 1 }}:00</strong><br>
                                    Recommended Staff: {{ $recommendation['recommended_staff'] }}<br>
                                    <small>{{ $recommendation['reason'] }}</small>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-2x text-success mb-3"></i>
                                <p class="text-muted">Current staffing levels are adequate</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-robot me-2"></i>AI-Powered Insights</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="insight-card p-3 border rounded">
                                    <h6><i class="fas fa-trending-up text-success me-2"></i>Growth Opportunity</h6>
                                    <p class="small">Weekend dinner orders show 25% higher average value. Consider premium menu items for Friday-Sunday evenings.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="insight-card p-3 border rounded">
                                    <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Cost Optimization</h6>
                                    <p class="small">Food waste can be reduced by 15% by adjusting portion sizes for items with high return rates.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="insight-card p-3 border rounded">
                                    <h6><i class="fas fa-clock text-info me-2"></i>Efficiency Tip</h6>
                                    <p class="small">Peak hour preparation can be improved by pre-preparing ingredients 30 minutes before rush hours.</p>
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
        // Demand Forecast Chart
        const demandCtx = document.getElementById('demandForecastChart').getContext('2d');
        new Chart(demandCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($demandForecast, 'date')) !!},
                datasets: [{
                    label: 'Predicted Orders',
                    data: {!! json_encode(array_column($demandForecast, 'predicted_orders')) !!},
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
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Sales Forecast Chart
        const salesForecastCtx = document.getElementById('salesForecastChart').getContext('2d');
        new Chart(salesForecastCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_slice(array_column($salesForecast, 'date'), 0, 7)) !!},
                datasets: [{
                    label: 'Predicted Revenue',
                    data: {!! json_encode(array_slice(array_column($salesForecast, 'predicted_revenue'), 0, 7)) !!},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
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

        function refreshForecasts() {
            // Implement forecast refresh
            location.reload();
        }

        function exportForecasts() {
            // Implement forecast export
            window.open('/analytics/export?type=forecasts&format=csv', '_blank');
        }

        function createPurchaseOrder(itemId) {
            // Implement purchase order creation
            alert('Purchase order functionality would be implemented here');
        }
    </script>
@endsection
