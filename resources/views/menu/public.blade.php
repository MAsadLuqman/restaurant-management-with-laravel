<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .menu-item-card {
            transition: transform 0.2s;
        }
        .menu-item-card:hover {
            transform: translateY(-5px);
        }
        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .floating-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 text-primary">Our Menu</h1>
        @if($table)
            <p class="lead">Table {{ $table->table_number }}</p>
        @endif
        <p class="text-muted">Delicious food made with love</p>
    </div>

    <!-- Menu Categories -->
    @foreach($categories as $category)
        <div class="category-section mb-5">
            <div class="category-header text-center">
                <h2>{{ $category->name }}</h2>
                @if($category->description)
                    <p class="mb-0">{{ $category->description }}</p>
                @endif
            </div>

            <div class="row">
                @foreach($category->activeMenuItems as $item)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card menu-item-card h-100 shadow-sm">
                            @if($item->image)
                                <img src="{{ Storage::url($item->image) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                            @endif
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title">{{ $item->name }}</h5>
                                    <span class="badge bg-success">₹{{ number_format($item->price, 2) }}</span>
                                </div>
                                <p class="card-text text-muted">{{ $item->description }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $item->preparation_time }} mins
                                    </small>
                                    <button class="btn btn-primary btn-sm add-to-cart"
                                            data-item-id="{{ $item->id }}"
                                            data-item-name="{{ $item->name }}"
                                            data-item-price="{{ $item->price }}">
                                        <i class="fas fa-plus me-1"></i>Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Floating Cart Button -->
    <div class="floating-cart">
        <button class="btn btn-success btn-lg rounded-circle" id="cartButton" data-bs-toggle="modal" data-bs-target="#cartModal" style="display: none;">
            <i class="fas fa-shopping-cart"></i>
            <span class="badge bg-danger" id="cartCount">0</span>
        </button>
    </div>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Your Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cartItems"></div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Total: ₹<span id="cartTotal">0.00</span></strong>
                </div>
            </div>
            <div class="modal-footer">
                <form id="orderForm">
                    @csrf
                    <input type="hidden" name="type" value="dine_in">
                    @if($table)
                        <input type="hidden" name="table_id" value="{{ $table->id }}">
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="customer_name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-6">
                            <input type="tel" name="customer_phone" class="form-control" placeholder="Phone Number">
                        </div>
                    </div>

                    <div class="mb-3">
                        <textarea name="special_instructions" class="form-control" rows="2" placeholder="Special instructions (optional)"></textarea>
                    </div>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                    <button type="submit" class="btn btn-success">Place Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let cart = [];
    let cartTotal = 0;

    $(document).ready(function() {
        // Add to cart
        $('.add-to-cart').click(function() {
            const itemId = $(this).data('item-id');
            const itemName = $(this).data('item-name');
            const itemPrice = parseFloat($(this).data('item-price'));

            // Check if item already exists
            const existingItem = cart.find(item => item.id === itemId);

            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: itemId,
                    name: itemName,
                    price: itemPrice,
                    quantity: 1
                });
            }

            updateCart();
        });

        // Update cart display
        function updateCart() {
            const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartTotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            $('#cartCount').text(cartCount);
            $('#cartTotal').text(cartTotal.toFixed(2));

            if (cartCount > 0) {
                $('#cartButton').show();
            } else {
                $('#cartButton').hide();
            }

            // Update cart items display
            const cartItemsHtml = cart.map(item => `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>${item.name}</strong>
                        <br><small>₹${item.price.toFixed(2)} x ${item.quantity}</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-2 decrease-qty" data-item-id="${item.id}">-</button>
                        <span>${item.quantity}</span>
                        <button class="btn btn-sm btn-outline-secondary ms-2 increase-qty" data-item-id="${item.id}">+</button>
                        <button class="btn btn-sm btn-outline-danger ms-2 remove-item" data-item-id="${item.id}">×</button>
                    </div>
                </div>
            `).join('');

            $('#cartItems').html(cartItemsHtml || '<p class="text-muted">Your cart is empty</p>');
        }

        // Cart item actions
        $(document).on('click', '.increase-qty', function() {
            const itemId = parseInt($(this).data('item-id'));
            const item = cart.find(item => item.id === itemId);
            if (item) {
                item.quantity++;
                updateCart();
            }
        });

        $(document).on('click', '.decrease-qty', function() {
            const itemId = parseInt($(this).data('item-id'));
            const item = cart.find(item => item.id === itemId);
            if (item && item.quantity > 1) {
                item.quantity--;
                updateCart();
            }
        });

        $(document).on('click', '.remove-item', function() {
            const itemId = parseInt($(this).data('item-id'));
            cart = cart.filter(item => item.id !== itemId);
            updateCart();
        });

        // Submit order
        $('#orderForm').submit(function(e) {
            e.preventDefault();

            if (cart.length === 0) {
                alert('Please add items to your cart');
                return;
            }

            const formData = new FormData(this);
            formData.append('items', JSON.stringify(cart.map(item => ({
                menu_item_id: item.id,
                quantity: item.quantity
            }))));

            $.ajax({
                url: '{{ route("orders.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Order placed successfully! Order #: ' + response.order.order_number);
                        cart = [];
                        updateCart();
                        $('#cartModal').modal('hide');
                        $('#orderForm')[0].reset();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error placing order. Please try again.');
                }
            });
        });
    });
</script>
</body>
</html>
