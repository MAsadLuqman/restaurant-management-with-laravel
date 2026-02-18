<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $twilioSid;
    protected $twilioToken;
    protected $twilioWhatsAppNumber;

    public function __construct()
    {
        $this->twilioSid = config('services.twilio.sid');
        $this->twilioToken = config('services.twilio.token');
        $this->twilioWhatsAppNumber = config('services.twilio.whatsapp_number');
    }

    public function sendOrderConfirmation(Order $order)
    {
        $message = $this->buildOrderConfirmationMessage($order);
        return $this->sendMessage($order->customer_phone, $message);
    }

    public function sendOrderStatusUpdate(Order $order)
    {
        $message = $this->buildStatusUpdateMessage($order);
        return $this->sendMessage($order->customer_phone, $message);
    }

    public function sendMessage($to, $message)
    {
        try {
            $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->twilioSid}/Messages.json", [
                    'From' => "whatsapp:{$this->twilioWhatsAppNumber}",
                    'To' => "whatsapp:{$to}",
                    'Body' => $message
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'message_sid' => $response->json('sid')
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'to' => $to,
                    'error' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    protected function buildOrderConfirmationMessage(Order $order)
    {
        $items = $order->orderItems->map(function ($item) {
            return "• {$item->menuItem->name} x{$item->quantity} - ₹{$item->total_price}";
        })->implode("\n");

        return "🍽️ *Order Confirmation*\n\n" .
            "Order #: {$order->order_number}\n" .
            "Type: " . ucfirst(str_replace('_', ' ', $order->type)) . "\n\n" .
            "*Items:*\n{$items}\n\n" .
            "Subtotal: ₹{$order->subtotal}\n" .
            "Tax: ₹{$order->tax_amount}\n" .
            "*Total: ₹{$order->total_amount}*\n\n" .
            "Estimated time: 30-45 minutes\n\n" .
            "Thank you for your order! 🙏";
    }

    protected function buildStatusUpdateMessage(Order $order)
    {
        $statusMessages = [
            'confirmed' => '✅ Your order has been confirmed and is being prepared.',
            'preparing' => '👨‍🍳 Your order is now being prepared in the kitchen.',
            'ready' => '🔔 Your order is ready for pickup/delivery!',
            'served' => '🍽️ Your order has been served. Enjoy your meal!',
            'completed' => '✨ Order completed. Thank you for dining with us!',
            'cancelled' => '❌ Your order has been cancelled. Please contact us for details.'
        ];

        $statusMessage = $statusMessages[$order->status] ?? 'Order status updated.';

        return "📱 *Order Update*\n\n" .
            "Order #: {$order->order_number}\n" .
            "Status: " . ucfirst($order->status) . "\n\n" .
            $statusMessage . "\n\n" .
            "Need help? Reply to this message.";
    }
}
