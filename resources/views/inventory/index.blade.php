@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Inventory Management</h1>
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Item
                    </a>
                </div>
            </div>
        </div>

        <!-- Inventory Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $items->count() }}</h3>
                        <p class="mb-0">Total Items</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ $lowStockItems->count() }}</h3>
                        <p class="mb-0">Low Stock</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>{{ $items->where('current_stock', '<=', 0)->count() }}</h3>
                        <p class="mb-0">Out of Stock</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>₹{{ number_format($items->sum('total_value'), 2) }}</h3>
                        <p class="mb-0">Total Value</p>
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

        <!-- Inventory Items -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Inventory Items</h5>
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
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
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
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-success adjust-stock"
                                                        data-item-id="{{ $item->id }}"
                                                        data-type="add">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger adjust-stock"
                                                        data-item-id="{{ $item->id }}"
                                                        data-type="subtract">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <a href="{{ route('inventory.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div class="modal fade" id="stockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="stockForm">
                    <div class="modal-body">
                        <input type="hidden" id="itemId" name="item_id">
                        <input type="hidden" id="adjustmentType" name="type">

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.adjust-stock').click(function() {
                const itemId = $(this).data('item-id');
                const type = $(this).data('type');

                $('#itemId').val(itemId);
                $('#adjustmentType').val(type);
                $('#stockModal .modal-title').text(type === 'add' ? 'Add Stock' : 'Remove Stock');
                $('#stockModal').modal('show');
            });

            $('#stockForm').submit(function(e) {
                e.preventDefault();

                const itemId = $('#itemId').val();
                const formData = $(this).serialize();

                $.ajax({
                    url: `/inventory/${itemId}/stock`,
                    method: 'PATCH',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#stockModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Error updating stock');
                    }
                });
            });

            // Auto-refresh low stock alerts
            setInterval(function() {
                $.get('{{ route("inventory.low-stock") }}', function(data) {
                    if (data.count > 0) {
                        // Update low stock indicator
                        console.log('Low stock items:', data.count);
                    }
                });
            }, 60000); // Check every minute
        });
    </script>
@endsection
