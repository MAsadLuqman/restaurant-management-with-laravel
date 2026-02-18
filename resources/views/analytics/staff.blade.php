@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Staff Analytics</h1>
                    <div class="btn-group" role="group">
                        <a href="{{ route('analytics.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                        <a href="{{ route('analytics.sales') }}" class="btn btn-outline-primary">Sales Analytics</a>
                        <a href="{{ route('analytics.inventory') }}" class="btn btn-outline-success">Inventory Analytics</a>
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

        <!-- Staff Performance Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Staff Performance ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Total Orders</th>
                                    <th>Total Sales</th>
                                    <th>Avg. Order Value</th>
                                    <th>Avg. Processing Time (min)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($staffPerformance as $staff)
                                    <tr>
                                        <td>{{ $staff->name }}</td>
                                        <td>{{ $staff->total_orders }}</td>
                                        <td>₹{{ number_format($staff->total_sales, 2) }}</td>
                                        <td>₹{{ number_format($staff->avg_order_value, 2) }}</td>
                                        <td>{{ number_format($staff->avg_processing_time, 1) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No staff performance data for this period.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Shift Analysis -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Sales by Shift</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="shiftSalesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Efficiency Metrics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Operational Efficiency Metrics</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Avg. Order Preparation Time:</strong>
                                <span>{{ number_format($efficiencyMetrics['avg_order_preparation_time'], 1) }} minutes</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Order Accuracy:</strong>
                                <span>{{ number_format($efficiencyMetrics['order_accuracy'], 1) }}%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Table Turnover (Avg. Dining Time):</strong>
                                <span>
                                @if($efficiencyMetrics['table_turnover']->count() > 0)
                                        {{ number_format($efficiencyMetrics['table_turnover']->avg('avg_dining_time'), 1) }} minutes
                                    @else
                                        N/A
                                    @endif
                            </span>
                            </li>
                        </ul>

                        <h6 class="mt-4">Table Turnover Details:</h6>
                        @if($efficiencyMetrics['table_turnover']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm mt-2">
                                    <thead>
                                    <tr>
                                        <th>Table</th>
                                        <th>Total Orders</th>
                                        <th>Avg. Dining Time (min)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($efficiencyMetrics['table_turnover'] as $table)
                                        <tr>
                                            <td>Table {{ $table->table_number }}</td>
                                            <td>{{ $table->total_orders }}</td>
                                            <td>{{ number_format($table->avg_dining_time, 1) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted small">No dine-in order data for table turnover analysis.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Shift Sales Chart
        const shiftSalesCtx = document.getElementById('shiftSalesChart').getContext('2d');
        new Chart(shiftSalesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($shiftAnalysis->pluck('shift')) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($shiftAnalysis->pluck('revenue')) !!},
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ],
                    borderWidth: 1
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
