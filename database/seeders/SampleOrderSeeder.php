<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;

class SampleOrderSeeder extends Seeder
{
    public function run()
    {
        $menuItems = MenuItem::all();
        $tables = Table::all();
        $waiters = User::whereHas('role', function($q) {
            $q->where('name', 'waiter');
        })->get();

        // Create orders for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $ordersPerDay = rand(15, 35); // Random orders per day

            for ($j = 0; $j < $ordersPerDay; $j++) {
                $orderTime = $date->copy()->addHours(rand(10, 22))->addMinutes(rand(0, 59));
                $table = $tables->random();
                $waiter = $waiters->random();

                $order = Order::create([
                    'order_number' => 'ORD-' . $orderTime->format('Ymd') . '-' . str_pad($j + 1, 3, '0', STR_PAD_LEFT),
                    'table_id' => rand(1, 3) == 1 ? $table->id : null,
                    'user_id' => $waiter->id,
                    'customer_name' => $this->generateCustomerName(),
                    'customer_phone' => '+1' . rand(1000000000, 9999999999),
                    'type' => $this->getRandomOrderType(),
                    'status' => $this->getRandomOrderStatus($orderTime),
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => rand(0, 1) ? rand(5, 20) : 0,
                    'total_amount' => 0,
                    'special_instructions' => rand(1, 4) == 1 ? $this->getRandomInstruction() : null,
                    'created_at' => $orderTime,
                    'updated_at' => $orderTime->copy()->addMinutes(rand(5, 45))
                ]);

                // Add random menu items
                $itemCount = rand(1, 6);
                $subtotal = 0;

                for ($k = 0; $k < $itemCount; $k++) {
                    $menuItem = $menuItems->random();
                    $quantity = rand(1, 3);
                    $price = $menuItem->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $menuItem->id,
                        'quantity' => $quantity,
                        'unit_price' => $price,
                        'total_price' => $price * $quantity,
                        'special_instructions' => rand(1, 8) == 1 ? $this->getRandomItemInstruction() : null
                    ]);

                    $subtotal += $price * $quantity;
                }

                // Update order totals
                $taxAmount = $subtotal * 0.08; // 8% tax
                $totalAmount = $subtotal + $taxAmount - $order->discount_amount;

                $order->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount
                ]);

                // Create payment if order is completed
                if ($order->status === 'completed') {
                    Payment::create([
                        'order_id' => $order->id,
                        'amount' => $totalAmount,
                        'payment_method' => $this->getRandomPaymentMethod(),
                        'status' => 'completed',
                        'transaction_id' => 'TXN-' . strtoupper(uniqid()),

                    ]);
                }
            }
        }
    }

    private function generateCustomerName()
    {
        $firstNames = ['John', 'Jane', 'Mike', 'Sarah', 'David', 'Lisa', 'Chris', 'Emma', 'Alex', 'Maria', 'Tom', 'Anna'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    private function getRandomOrderType()
    {
        $types = ['dine_in', 'takeaway', 'delivery'];
        return $types[array_rand($types)];
    }

    private function getRandomOrderStatus($orderTime)
    {
        $daysDiff = Carbon::now()->diffInDays($orderTime);

        if ($daysDiff > 1) {
            $statuses = ['completed', 'completed', 'completed', 'cancelled'];
        } else {
            $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];
        }

        return $statuses[array_rand($statuses)];
    }

    private function getRandomInstruction()
    {
        $instructions = [
            'Extra spicy please',
            'No onions',
            'Gluten-free preparation',
            'Extra cheese',
            'Well done',
            'On the side',
            'Light on salt',
            'Extra sauce'
        ];

        return $instructions[array_rand($instructions)];
    }

    private function getRandomItemInstruction()
    {
        $instructions = [
            'Extra crispy',
            'Medium rare',
            'No pickles',
            'Extra sauce',
            'Light seasoning',
            'Well cooked'
        ];

        return $instructions[array_rand($instructions)];
    }

    private function getRandomPaymentMethod()
    {
        $methods = ['cash', 'card', 'digital_wallet', 'bank_transfer'];
        return $methods[array_rand($methods)];
    }
}
