@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>Reservation Details</h4>
                            <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-primary">Edit Reservation</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Customer Name:</strong> {{ $reservation->customer_name }}
                        </div>
                        <div class="mb-3">
                            <strong>Customer Phone:</strong> {{ $reservation->customer_phone }}
                        </div>
                        <div class="mb-3">
                            <strong>Customer Email:</strong> {{ $reservation->customer_email ?? 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <strong>Table:</strong> Table {{ $reservation->table->table_number }} (Capacity: {{ $reservation->table->capacity }})
                        </div>
                        <div class="mb-3">
                            <strong>Reservation Date & Time:</strong> {{ $reservation->reservation_date->format('M d, Y h:i A') }}
                        </div>
                        <div class="mb-3">
                            <strong>Party Size:</strong> {{ $reservation->party_size }} guests
                        </div>
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $reservation->status === 'confirmed' ? 'success' : ($reservation->status === 'pending' ? 'warning' : ($reservation->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                            {{ ucfirst($reservation->status) }}
                        </span>
                        </div>
                        <div class="mb-3">
                            <strong>Special Requests:</strong> {{ $reservation->special_requests ?? 'None' }}
                        </div>
                        <div class="mb-3">
                            <strong>Booked On:</strong> {{ $reservation->created_at->format('M d, Y h:i A') }}
                        </div>

                        <a href="{{ route('reservations.index') }}" class="btn btn-secondary mt-3">Back to Reservations</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
