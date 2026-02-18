@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Order Details #{{ $order->id }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('orders.edit', $order->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
                    Edit Order
                </a>
                <a href="{{ route('orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
                    Back to Orders
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Order Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600"><strong class="text-gray-800">Order ID:</strong> {{ $order->id }}</p>
                    <p class="text-gray-600"><strong class="text-gray-800">Table:</strong> {{ $order->table ? $order->table->name : 'N/A' }}</p>
                    <p class="text-gray-600"><strong class="text-gray-800">Status:</strong>
                        <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full {{
                        $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                        ($order->status === 'preparing' ? 'bg-blue-100 text-blue-800' :
                        ($order->status === 'completed' ? 'bg-green-100 text-green-800' :
                        ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')))
                    }}">
                        {{ ucfirst($order->status) }}
                    </span>
                    </p>
                </div>
                <div>
                    <p class="text-gray-600"><strong class="text-gray-800">Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                    <p class="text-gray-600"><strong class="text-gray-800">Order Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    <p class="text-gray-600"><strong class="text-gray-800">Last Updated:</strong> {{ $order->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Order Items</h2>
            @if ($order->orderItems->isEmpty())
                <p class="text-gray-600 text-center">No items in this order.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price Per Item
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subtotal
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->menuItem->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($item->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($item->quantity * $item->price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
