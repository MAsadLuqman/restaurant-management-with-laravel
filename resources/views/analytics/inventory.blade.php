@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Inventory Analytics</h1>
                    <div class="btn-group" role="group">
                        <a href="{{ route('analytics.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                        <a href="{{ route('analytics.sales') }}" class="btn btn-outline-primary">Sales Analytics</a>
                        <a href="{{ route('analytics.staff') }}" class="btn btn-outline-info">Staff Analytics</a>
                        <a href="{{ route('analytics.predictive') }}" class="btn btn-outline-warning">Predictive Analytics</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Value & Stock Levels -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Total Inventory Value</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3>₹{{ number_format($inventoryValue['total_value'], 2) }}</h3>
                        <p class="text-muted">Current estimated value of all active inventory</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Stock Level Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="stockLevelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock & Demand Forecast -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Low Stock Items (Action Required)</h5>
                    </div>
                    <div class="card-body">
                        @if($stockLevels->has('low_stock') || $stockLevels->has('out_of_stock'))
                            <ul class="list-group">
                                @if($stockLevels->has('out_of_stock'))
                                    @foreach($stockLevels['out_of_stock'] as $item)
                                        <li class="list-group-item list-group-item-danger d-flex justify-content-between align-items-center">
                                            <div><strong>{{ $item->name }}</strong> (Out of Stock)</div>
                                            <span class="badge bg-danger">0 {{ $item->unit }}</span>
                                        </li>
                                    @endforeach
                                @endif
                                @if($stockLevels->has('low_stock'))
                                    @foreach($stockLevels['low_stock'] as $item)
                                        <li class="list-group-item list-group-item-warning d-flex justify-content-between align-items-center">
                                            <div><strong>{{ $item->name }}</strong> (Low Stock)</div>
                                            <span class="badge bg-warning">{{ $item->current_stock }} {{ $item->unit }} / Min: {{ $item->minimum_stock }} {{ $item->unit }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5>All stock levels are healthy!</h5>
                                <p class="text-muted">No low or out-of-stock items currently.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Demand Forecast (Next 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        @if($demandForecast->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Current Stock</th>
                                        <th>Days Remaining</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($demandForecast as $item)
                                        <tr class="{{ $item->days_remaining < 3 ? 'table-danger' : ($item->days_remaining < 7 ? 'table-warning' : '') }}">
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->current_stock }} {{ $item->unit }}</td>
                                            <td>{{ number_format($item->days_remaining, 1) }} days</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                                <p class="text-muted">No immediate demand concerns for inventory items.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Turnover & Wastage Analysis -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Inventory Turnover (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        @if($inventoryTurnover->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Consumed</th>
                                        <th>Turnover Ratio</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($inventoryTurnover as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ number_format($item->total_consumed, 2) }} {{ $item->unit }}</td>
                                            <td>{{ number_format($item->turnover_ratio, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-sync-alt fa-3x text-secondary mb-3"></i>
                                <p class="text-muted">No inventory turnover data for the selected period.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Potential Wastage Analysis</h5>
                    </div>
                    <div class="card-body">
                        @if($wastageAnalysis->count() > 0)
                            <ul class="list-group">
                                @foreach($wastageAnalysis as $item)
                                    <li class="list-group-item list-group-item-danger d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $item->name }}</strong>
                                            <br><small class="text-muted">Current: {{ $item->current_stock }} {{ $item->unit }}</small>
                                        </div>
                                        <span class="badge bg-danger">Est. ₹{{ number_format($item->potential_wastage, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                                <p class="text-muted">No significant potential wastage identified.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Stock Level Distribution Chart
        const stockLevelCtx = document.getElementById('stockLevelChart').getContext('2d');
        new Chart(stockLevelCtx, {
            type: 'doughnut',
            data: {
                labels: ['Out of Stock', 'Low Stock', 'Medium Stock', 'High Stock'],
                datasets: [{
                    data: [
                        {{ $stockLevels->has('out_of_stock') ? $stockLevels['out_of_stock']->count() : 0 }},
                        {{ $stockLevels->has('low_stock') ? $stockLevels['low_stock']->count() : 0 }},
                        {{ $stockLevels->has('medium_stock') ? $stockLevels['medium_stock']->count() : 0 }},
                        {{ $stockLevels->has('high_stock') ? $stockLevels['high_stock']->count() : 0 }}
                    ],
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.8)', // Danger
                        'rgba(255, 193, 7, 0.8)', // Warning
                        'rgba(23, 162, 184, 0.8)', // Info
                        'rgba(40, 167, 69, 0.8)'  // Success
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
    </script>
@endsection
