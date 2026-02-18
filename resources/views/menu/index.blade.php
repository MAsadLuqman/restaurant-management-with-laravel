@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Menu Management</h1>
                    <div>
                        <a href="{{ route('categories.create') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-plus me-2"></i>Add Category
                        </a>
                        <a href="{{ route('menu-items.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Menu Item
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form method="GET">
                            <div class="row">
                                <div class="col-md-6">
                                    <select name="category" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('menu-items.index') }}" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        <div class="row">
            @foreach($menuItems as $item)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">{{ $item->name }}</h5>
                                <span class="badge bg-{{ $item->is_available ? 'success' : 'danger' }}">
                            {{ $item->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                            </div>
                            <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                            <div class="mb-2">
                                <strong>Category:</strong> {{ $item->category->name }}<br>
                                <strong>Price:</strong> ₹{{ number_format($item->price, 2) }}<br>
                                <strong>Prep Time:</strong> {{ $item->preparation_time }} mins
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('menu-items.show', $item) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('menu-items.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <button class="btn btn-sm btn-outline-{{ $item->is_available ? 'warning' : 'success' }} toggle-availability"
                                        data-item-id="{{ $item->id }}">
                                    {{ $item->is_available ? 'Disable' : 'Enable' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                {{ $menuItems->links() }}
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.toggle-availability').click(function() {
                const button = $(this);
                const itemId = button.data('item-id');

                $.ajax({
                    url: `/menu-items/${itemId}/toggle-availability`,
                    method: 'PATCH',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('Error updating item availability');
                    }
                });
            });
        });
    </script>
@endsection
