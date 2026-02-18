@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Create New Order</h4>
                    </div>
                    <div class="card-body">
                        <form id="orderForm">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Order Type</label>
                                    <select name="type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="dine_in">Dine In</option>
                                        <option value="takeaway">Takeaway</option>
                                        <option value="delivery">Delivery</option>
                                    </select>
                                </div>
                                <div class="col-md-4" id="tableSelection" style="display: none;">
                                    <label class="form-label">Table</label>
                                    <select name="table_id" class="form-select">
                                        <option value="">Select Table</option>
                                        @foreach($tables as $table)
                                            <option value="{{ $table->id }}">Table {{ $table->table_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3" id="customerDetails" style="display: none;">
                                <div class="col-md-4">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" name="customer_name" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Customer Phone</label>
                                    <input type="text" name="customer_phone" class="form-control">
                                </div>
                                <div class="col-md-4" id="addressField" style="display: none;">
                                    <label class="form-label">Address</label>
                                    <textarea name="customer_address" class="form-control" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Menu Items</label>
                                <div class="row">
                                    @foreach($menuItems->groupBy('category.name') as $categoryName => $items)
                                        <div class="col-md-6 mb-3">
                                            <h6>{{ $categoryName }}</h6>
                                            @foreach($items as $item)
                                                <div class="menu-item border p-2 mb-2" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ $item->name }}</strong>
                                                            <br><small class="text-muted">₹{{ $item->price }}</small>
                                                        </div>
                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-outline-primary add-item">Add</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Special Instructions</label>
                                <textarea name="special_instructions" class="form-control" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div id="orderItems">
                            <p class="text-muted">No items added yet</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total: ₹<span id="orderTotal">0.00</span></strong>
                        </div>
                        <button type="button" id="submitOrder" class="btn btn-primary w-100 mt-3" disabled>Place Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let orderItems = [];
            let orderTotal = 0;

            // Show/hide fields based on order type
            $('select[name="type"]').change(function() {
                const type = $(this).val();

                if (type === 'dine_in') {
                    $('#tableSelection').show();
                    $('#customerDetails').hide();
                    $('#addressField').hide();
                } else if (type === 'takeaway') {
                    $('#tableSelection').hide();
                    $('#customerDetails').show();
                    $('#addressField').hide();
                } else if (type === 'delivery') {
                    $('#tableSelection').hide();
                    $('#customerDetails').show();
                    $('#addressField').show();
                }
            });

            // Add item to order
            $('.add-item').click(function() {
                const menuItem = $(this).closest('.menu-item');
                const itemId = menuItem.data('item-id');
                const itemName = menuItem.find('strong').text();
                const itemPrice = parseFloat(menuItem.data('price'));

                // Check if item already exists
                const existingItem = orderItems.find(item => item.menu_item_id === itemId);

                if (existingItem) {
                    existingItem.quantity++;
                    existingItem.total = existingItem.quantity * existingItem.price;
                } else {
                    orderItems.push({
                        menu_item_id: itemId,
                        name: itemName,
                        price: itemPrice,
                        quantity: 1,
                        total: itemPrice
                    });
                }

                updateOrderSummary();
            });

            // Update order summary
            function updateOrderSummary() {
                const itemsHtml = orderItems.map(item => `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong>${item.name}</strong>
                    <br><small>₹${item.price} x ${item.quantity}</small>
                </div>
                <div>
                    <span>₹${item.total.toFixed(2)}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-item" data-item-id="${item.menu_item_id}">×</button>
                </div>
            </div>
        `).join('');

                orderTotal = orderItems.reduce((sum, item) => sum + item.total, 0);

                $('#orderItems').html(itemsHtml || '<p class="text-muted">No items added yet</p>');
                $('#orderTotal').text(orderTotal.toFixed(2));
                $('#submitOrder').prop('disabled', orderItems.length === 0);

                // Bind remove item events
                $('.remove-item').click(function() {
                    const itemId = parseInt($(this).data('item-id'));
                    orderItems = orderItems.filter(item => item.menu_item_id !== itemId);
                    updateOrderSummary();
                });
            }

            // Submit order
            $('#submitOrder').click(function() {
                const formData = new FormData(document.getElementById('orderForm'));
                formData.append('items', JSON.stringify(orderItems));

                $.ajax({
                    url: '{{ route("orders.store") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert('Order created successfully!');
                            window.location.href = '{{ route("orders.index") }}';
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error creating order. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection
