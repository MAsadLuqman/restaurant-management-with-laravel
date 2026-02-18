@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>Category Details</h4>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary">Edit Category</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                @if($category->image)
                                    <img src="{{ Storage::url($category->image) }}" class="img-fluid rounded mb-3" alt="{{ $category->name }}">
                                @else
                                    <img src="/placeholder.svg?height=200&width=200" class="img-fluid rounded mb-3" alt="Placeholder Image">
                                @endif
                            </div>
                            <div class="col-md-8">
                                <h3 class="mb-3">{{ $category->name }}</h3>
                                <p class="text-muted">{{ $category->description }}</p>
                                <div class="mb-2">
                                    <strong>Status:</strong>
                                    <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                </div>

                                <hr>

                                <h5>Menu Items in this Category</h5>
                                @if($category->menuItems->count() > 0)
                                    <ul class="list-group list-group-flush">
                                        @foreach($category->menuItems as $item)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a href="{{ route('menu-items.show', $item) }}">{{ $item->name }}</a>
                                                <span class="badge bg-info">₹{{ number_format($item->price, 2) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No menu items found in this category.</p>
                                @endif

                                <a href="{{ route('categories.index') }}" class="btn btn-secondary mt-3">Back to Categories</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
