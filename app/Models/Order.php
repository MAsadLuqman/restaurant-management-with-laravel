<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'type', 'status', 'table_id', 'user_id',
        'customer_name', 'customer_phone', 'customer_address',
        'subtotal', 'tax_amount', 'discount_amount', 'total_amount',
        'special_instructions', 'estimated_completion_time'
    ];

    protected $casts = [
        'estimated_completion_time' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function generateOrderNumber()
    {
        $lastOrder = self::latest()->first();
        $lastNumber = $lastOrder ? intval(substr($lastOrder->order_number, 3)) : 0;
        return 'ORD' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    public function calculateTotal()
    {
        $subtotal = $this->orderItems->sum('total_price');
        $taxAmount = $subtotal * 0.18; // 18% GST
        $total = $subtotal + $taxAmount - $this->discount_amount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total
        ]);

        return $total;
    }
}
