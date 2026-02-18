<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['orderItems.menuItem', 'table', 'user']);
    }

    public function broadcastOn()
    {
        return [
            new Channel('kitchen-display'),
            new Channel('orders'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'order' => $this->order,
            'timestamp' => now()->toISOString(),
        ];
    }
}
