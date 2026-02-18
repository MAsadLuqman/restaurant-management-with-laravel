<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_number', 'capacity', 'status', 'qr_code'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function currentOrder()
    {
        return $this->hasOne(Order::class)->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready']);
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function generateQRCode()
    {
        $qrCode = 'TABLE_' . $this->table_number . '_' . time();
        $this->update(['qr_code' => $qrCode]);
        return $qrCode;
    }
}
