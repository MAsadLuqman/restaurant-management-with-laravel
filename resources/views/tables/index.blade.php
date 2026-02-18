@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Tables Management</h1>
                    <a href="{{ route('tables.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Table
                    </a>
                </div>
            </div>
        </div>

        <!-- Table Status Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ $tables->where('status', 'available')->count() }}</h3>
                        <p class="mb-0">Available</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ $tables->where('status', 'occupied')->count() }}</h3>
                        <p class="mb-0">Occupied</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>{{ $tables->where('status', 'reserved')->count() }}</h3>
                        <p class="mb-0">Reserved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $tables->where('status', 'maintenance')->count() }}</h3>
                        <p class="mb-0">Maintenance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="row">
            @foreach($tables as $table)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card border-{{ $table->status === 'available' ? 'success' : ($table->status === 'occupied' ? 'danger' : ($table->status === 'reserved' ? 'warning' : 'secondary')) }}">
                        <div class="card-header bg-{{ $table->status === 'available' ? 'success' : ($table->status === 'occupied' ? 'danger' : ($table->status === 'reserved' ? 'warning' : 'secondary')) }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Table {{ $table->table_number }}</h5>
                                <span class="badge bg-light text-dark">{{ $table->capacity }} seats</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $table->status === 'available' ? 'success' : ($table->status === 'occupied' ? 'danger' : ($table->status === 'reserved' ? 'warning' : 'secondary')) }}">
                            {{ ucfirst($table->status) }}
                        </span>
                            </div>

                            @if($table->currentOrder)
                                <div class="mb-3">
                                    <strong>Current Order:</strong><br>
                                    <small>{{ $table->currentOrder->order_number }}</small><br>
                                    <small>₹{{ number_format($table->currentOrder->total_amount, 2) }}</small>
                                </div>
                            @endif

                            @if($table->reservations->count() > 0)
                                <div class="mb-3">
                                    <strong>Today's Reservations:</strong><br>
                                    @foreach($table->reservations as $reservation)
                                        <small>{{ $reservation->reservation_date->format('h:i A') }} - {{ $reservation->customer_name }}</small><br>
                                    @endforeach
                                </div>
                            @endif

                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('tables.show', $table) }}">View Details</a></li>
                                    <li><a class="dropdown-item" href="{{ route('tables.edit', $table) }}">Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item status-update" href="#" data-table-id="{{ $table->id }}" data-status="available">Mark Available</a></li>
                                    <li><a class="dropdown-item status-update" href="#" data-table-id="{{ $table->id }}" data-status="occupied">Mark Occupied</a></li>
                                    <li><a class="dropdown-item status-update" href="#" data-table-id="{{ $table->id }}" data-status="maintenance">Mark Maintenance</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item generate-qr" href="#" data-table-id="{{ $table->id }}">Generate QR Code</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeContainer"></div>
                    <p class="mt-3">Scan this QR code to view the menu</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Update table status
            $('.status-update').click(function(e) {
                e.preventDefault();
                const tableId = $(this).data('table-id');
                const status = $(this).data('status');

                $.ajax({
                    url: `/tables/${tableId}/status`,
                    method: 'PATCH',
                    data: { status: status },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('Error updating table status');
                    }
                });
            });

            // Generate QR Code
            $('.generate-qr').click(function(e) {
                e.preventDefault();
                const tableId = $(this).data('table-id');

                $.ajax({
                    url: `/tables/${tableId}/qr`,
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            $('#qrCodeContainer').html(response.qr_code);
                            $('#qrModal').modal('show');
                        }
                    },
                    error: function() {
                        alert('Error generating QR code');
                    }
                });
            });
        });
    </script>
@endsection
