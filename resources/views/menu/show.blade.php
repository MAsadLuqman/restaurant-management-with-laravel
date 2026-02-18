@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>Menu Item Details</h4>
                            <a href="{{ route('menu-items.edit', $menuItem) }}" class="btn btn-sm btn-primary">Edit Item</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                @if($menuItem->image)
                                    <img src="{{ Storage::url($menuItem->image) }}" class="img-fluid rounded mb-3" alt="{{ $menuItem->name }}">
                                @else
                                    <img src="/placeholder.svg?height=200&width=200" class="img-fluid rounded mb-3" alt="Placeholder Image">
                                @endif
                            </div>
                            <div class="col-md-8">
                                <h3 class="mb-3">{{ $menuItem->name }}</h3>
                                <p class="text-muted">{{ $menuItem->description }}</p>
                                <div class="mb-2">
                                    <strong>Price:</strong> ₹{{ number_format($menuItem->price, 2) }}
                                </div>
                                <div class="mb-2">
                                    <strong>Category:</strong> {{ $menuItem->category->name }}
                                </div>
                                <div class="mb-2">
                                    <strong>Preparation Time:</strong> {{ $menuItem->preparation_time }} minutes
                                </div>
                                <div class="mb-2">
                                    <strong>Availability:</strong>
                                    <span class="badge bg-{{ $menuItem->is_available ? 'success' : 'danger' }}">
                                    {{ $menuItem->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                                </div>

                                <hr>

                                <h5>Required Inventory Items</h5>
                                @if($menuItem->inventoryItems->count() > 0)
                                    <ul class="list-group list-group-flush">
                                        @foreach($menuItem->inventoryItems as $inventoryItem)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $inventoryItem->name }}
                                                <span class="badge bg-info">{{ $inventoryItem->pivot->quantity_required }} {{ $inventoryItem->unit }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No specific inventory items linked.</p>
                                @endif

                                <a href="{{ route('menu-items.index') }}" class="btn btn-secondary mt-3">Back to Menu</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
