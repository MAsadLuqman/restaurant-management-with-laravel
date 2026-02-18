@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Inventory Report</h1>
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Inventory Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $summary['total_items'] }}</h3>
                        <p class="mb-0">Total Items</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ $summary['low_stock_items'] }}</h3>
                        <p class="mb-0">Low Stock Items</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>{{ $summary['out_of_stock'] }}</h3>
                        <p class="mb-0">Out of Stock Items</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>₹{{ number_format($summary['total_value'], 2) }}</h3>
                        <p class="mb-0">Total Inventory Value</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        @if($lowStockItems->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert</h5>
                        <p>The following items are running low on stock:</p>
                        <ul class="mb-0">
                            @foreach($lowStockItems as $item)
                                <li>{{ $item->name }} - Current: {{ $item->current_stock }} {{ $item->unit }}, Minimum: {{ $item->minimum_stock }} {{ $item->unit }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Inventory Items Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Detailed Inventory List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Current Stock</th>
                                    <th>Minimum Stock</th>
                                    <th>Unit</th>
                                    <th>Unit Cost</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($items as $item)
                                    <tr class="{{ $item->isLowStock() ? 'table-warning' : '' }}">
                                        <td>
                                            <strong>{{ $item->name }}</strong>
                                            @if($item->description)
                                                <br><small class="text-muted">{{ $item->description }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->current_stock }}</td>
                                        <td>{{ $item->minimum_stock }}</td>
                                        <td>{{ $item->unit }}</td>
                                        <td>₹{{ number_format($item->unit_cost, 2) }}</td>
                                        <td>₹{{ number_format($item->total_value, 2) }}</td>
                                        <td>
                                            @if($item->current_stock <= 0)
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @elseif($item->isLowStock())
                                                <span class="badge bg-warning">Low Stock</span>
                                            @else
                                                <span class="badge bg-success">In Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No inventory items found.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
