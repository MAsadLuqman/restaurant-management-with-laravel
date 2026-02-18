@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Reservations</h1>
                    <a href="{{ route('reservations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Reservation
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's Reservations -->
        @if($todayReservations->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Today's Reservations</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($todayReservations as $reservation)
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-{{ $reservation->status === 'confirmed' ? 'success' : ($reservation->status === 'pending' ? 'warning' : 'secondary') }}">
                                            <div class="card-body">
                                                <h6>{{ $reservation->customer_name }}</h6>
                                                <p class="mb-1">
                                                    <i class="fas fa-clock me-2"></i>{{ $reservation->reservation_date->format('h:i A') }}<br>
                                                    <i class="fas fa-table me-2"></i>Table {{ $reservation->table->table_number }}<br>
                                                    <i class="fas fa-users me-2"></i>{{ $reservation->party_size }} guests
                                                </p>
                                                <span class="badge bg-{{ $reservation->status === 'confirmed' ? 'success' : ($reservation->status === 'pending' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                                <div class="mt-2">
                                                    <button class="btn btn-sm btn-outline-primary update-status"
                                                            data-reservation-id="{{ $reservation->id }}"
                                                            data-status="confirmed">Confirm</button>
                                                    <button class="btn btn-sm btn-outline-success update-status"
                                                            data-reservation-id="{{ $reservation->id }}"
                                                            data-status="seated">Seat</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- All Reservations -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>All Reservations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Date & Time</th>
                                    <th>Table</th>
                                    <th>Party Size</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($reservations as $reservation)
                                    <tr>
                                        <td>{{ $reservation->customer_name }}</td>
                                        <td>{{ $reservation->customer_phone }}</td>
                                        <td>{{ $reservation->reservation_date->format('M d, Y h:i A') }}</td>
                                        <td>Table {{ $reservation->table->table_number }}</td>
                                        <td>{{ $reservation->party_size }}</td>
                                        <td>
                                        <span class="badge bg-{{ $reservation->status === 'confirmed' ? 'success' : ($reservation->status === 'pending' ? 'warning' : ($reservation->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                @if($reservation->status === 'pending')
                                                    <button class="btn btn-sm btn-outline-success update-status"
                                                            data-reservation-id="{{ $reservation->id }}"
                                                            data-status="confirmed">Confirm</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No reservations found</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                {{ $reservations->links() }}
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.update-status').click(function() {
                const button = $(this);
                const reservationId = button.data('reservation-id');
                const status = button.data('status');

                $.ajax({
                    url: `/reservations/${reservationId}/status`,
                    method: 'PATCH',
                    data: { status: status },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('Error updating reservation status');
                    }
                });
            });
        });
    </script>
@endsection
