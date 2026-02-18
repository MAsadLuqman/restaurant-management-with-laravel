@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>Table {{ $table->table_number }} Details</h4>
                            <a href="{{ route('tables.edit', $table) }}" class="btn btn-sm btn-primary">Edit Table</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Table Number:</strong> {{ $table->table_number }}
                        </div>
                        <div class="mb-3">
                            <strong>Capacity:</strong> {{ $table->capacity }} seats
                        </div>
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $table->status === 'available' ? 'success' : ($table->status === 'occupied' ? 'danger' : ($table->status === 'reserved' ? 'warning' : 'secondary')) }}">
                            {{ ucfirst($table->status) }}
                        </span>
                        </div>
                        <div class="mb-3">
                            <strong>QR Code:</strong>
                            @if($table->qr_code)
                                <a href="#" class="btn btn-sm btn-outline-info generate-qr" data-table-id="{{ $table->id }}">View QR Code</a>
                            @else
                                <span class="text-muted">Not generated</span>
                                <a href="#" class="btn btn-sm btn-outline-primary generate-qr" data-table-id="{{ $table->id }}">Generate QR Code</a>
                            @endif
                        </div>

                        <hr>

                        <h5>Current Order</h5>
                        @if($table->currentOrder)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <strong>Order #{{ $table->currentOrder->order_number }}</strong><br>
                                    Total: ₹{{ number_format($table->currentOrder->total_amount, 2) }}<br>
                                    Status: <span class="badge bg-warning">{{ ucfirst($table->currentOrder->status) }}</span>
                                    <br>
                                    <a href="{{ route('orders.show', $table->currentOrder) }}" class="btn btn-sm btn-info mt-2">View Order</a>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">No active order for this table.</p>
                        @endif

                        <hr>

                        <h5>Upcoming Reservations</h5>
                        @if($table->reservations->count() > 0)
                            <ul class="list-group">
                                @foreach($table->reservations as $reservation)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $reservation->customer_name }}</strong> ({{ $reservation->party_size }} guests)<br>
                                            <small>{{ $reservation->reservation_date->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <span class="badge bg-{{ $reservation->status === 'confirmed' ? 'success' : ($reservation->status === 'pending' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No upcoming reservations for this table.</p>
                        @endif

                        <a href="{{ route('tables.index') }}" class="btn btn-secondary mt-3">Back to Tables</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">QR Code for Table {{ $table->table_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeContainer"></div>
                    <p class="mt-3">Scan this QR code to view the menu</p>
                    <p class="small text-muted">{{ route('menu.public', ['table' => $table->qr_code]) }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
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
    @endpush
@endsection
