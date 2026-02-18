@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit Order #{{ $order->id }}</h1>
            <a href="{{ route('orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
                Back to Orders
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg p-6">
            <form action="{{ route('orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="table_id" class="block text-gray-700 text-sm font-bold mb-2">Table:</label>
                    <select name="table_id" id="table_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('table_id') border-red-500 @enderror">
                        <option value="">Select a Table</option>
                        @foreach ($tables as $table)
                            <option value="{{ $table->id }}" {{ $order->table_id == $table->id ? 'selected' : '' }}>
                                {{ $table->name }} (Capacity: {{ $table->capacity }})
                            </option>
                        @endforeach
                    </select>
                    @error('table_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                    <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('status') border-red-500 @enderror">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Items</h2>
                <div id="order-items-container">
                    @foreach ($order->orderItems as $index => $item)
                        <div class="flex items-center space-x-4 mb-4 p-4 border rounded-lg bg-gray-50" data-item-index="{{ $index }}">
                            <div class="flex-1">
                                <label for="items[{{ $index }}][menu_item_id]" class="block text-gray-700 text-sm font-bold mb-2">Menu Item:</label>
                                <select name="items[{{ $index }}][menu_item_id]" class="menu-item-select shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select Menu Item</option>
                                    @foreach ($menuItems as $menuItem)
                                        <option value="{{ $menuItem->id }}" data-price="{{ $menuItem->price }}" {{ $item->menu_item_id == $menuItem->id ? 'selected' : '' }}>
                                            {{ $menuItem->name }} (${{ number_format($menuItem->price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-24">
                                <label for="items[{{ $index }}][quantity]" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline quantity-input">
                            </div>
                            <div class="w-24">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Price:</label>
                                <span class="item-price block py-2 px-3 text-gray-700">${{ number_format($item->price, 2) }}</span>
                            </div>
                            <button type="button" class="remove-item-btn bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out mt-6">Remove</button>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="add-item-btn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out mt-4">
                    Add Item
                </button>

                <div class="mt-6 text-right">
                    <p class="text-lg font-bold text-gray-800">Total Amount: $<span id="total-amount">{{ number_format($order->total_amount, 2) }}</span></p>
                </div>

                <div class="flex justify-end mt-8">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
                        Update Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderItemsContainer = document.getElementById('order-items-container');
            const addItemBtn = document.getElementById('add-item-btn');
            const totalAmountSpan = document.getElementById('total-amount');
            let itemIndex = {{ count($order->orderItems) }};

            function calculateTotal() {
                let total = 0;
                orderItemsContainer.querySelectorAll('.flex.items-center').forEach(itemDiv => {
                    const quantity = parseFloat(itemDiv.querySelector('.quantity-input').value) || 0;
                    const priceText = itemDiv.querySelector('.item-price').textContent;
                    const price = parseFloat(priceText.replace('$', '')) || 0;
                    total += quantity * price;
                });
                totalAmountSpan.textContent = total.toFixed(2);
            }

            function updateItemPrice(itemDiv) {
                const selectElement = itemDiv.querySelector('.menu-item-select');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const priceSpan = itemDiv.querySelector('.item-price');
                if (selectedOption && selectedOption.dataset.price) {
                    priceSpan.textContent = '$' + parseFloat(selectedOption.dataset.price).toFixed(2);
                } else {
                    priceSpan.textContent = '$0.00';
                }
                calculateTotal();
            }

            function setupItemListeners(itemDiv) {
                itemDiv.querySelector('.menu-item-select').addEventListener('change', function() {
                    updateItemPrice(itemDiv);
                });
                itemDiv.querySelector('.quantity-input').addEventListener('input', calculateTotal);
                itemDiv.querySelector('.remove-item-btn').addEventListener('click', function() {
                    itemDiv.remove();
                    calculateTotal();
                });
            }

            // Setup listeners for existing items
            orderItemsContainer.querySelectorAll('.flex.items-center').forEach(setupItemListeners);

            addItemBtn.addEventListener('click', function() {
                const newItemDiv = document.createElement('div');
                newItemDiv.classList.add('flex', 'items-center', 'space-x-4', 'mb-4', 'p-4', 'border', 'rounded-lg', 'bg-gray-50');
                newItemDiv.dataset.itemIndex = itemIndex;
                newItemDiv.innerHTML = `
                <div class="flex-1">
                    <label for="items[${itemIndex}][menu_item_id]" class="block text-gray-700 text-sm font-bold mb-2">Menu Item:</label>
                    <select name="items[${itemIndex}][menu_item_id]" class="menu-item-select shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select Menu Item</option>
                        @foreach ($menuItems as $menuItem)
                <option value="{{ $menuItem->id }}" data-price="{{ $menuItem->price }}">
                                {{ $menuItem->name }} (${{ number_format($menuItem->price, 2) }})
                            </option>
                        @endforeach
                </select>
            </div>
            <div class="w-24">
                <label for="items[${itemIndex}][quantity]" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                    <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline quantity-input">
                </div>
                <div class="w-24">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Price:</label>
                    <span class="item-price block py-2 px-3 text-gray-700">$0.00</span>
                </div>
                <button type="button" class="remove-item-btn bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out mt-6">Remove</button>
            `;
                orderItemsContainer.appendChild(newItemDiv);
                setupItemListeners(newItemDiv);
                updateItemPrice(newItemDiv); // Set initial price for new item
                itemIndex++;
            });

            calculateTotal(); // Initial calculation on page load
        });
    </script>
@endsection
