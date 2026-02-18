<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'customer_phone', 'customer_email',
        'table_id', 'reservation_date', 'party_size', 'status',
        'special_requests'
    ];

    protected $casts = [
        'reservation_date' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>', now())
            ->where('status', '!=', 'cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reservation_date', today());
    }

    public function isUpcoming()
    {
        return $this->reservation_date > now() && $this->status !== 'cancelled';
    }
}
