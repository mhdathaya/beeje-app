<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reservation_number',
        'reservation_date',
        'reservation_time',
        'people_count',
        'notes',
        'payment_method',
        'payment_status',
        'snap_token',
        'total_amount'  // Add this line
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime:H:i',
        'total_amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function calculateTotal()
    {
        if ($this->order) {
            $this->total_amount = $this->order->total_amount;
            $this->save();
        }
        return $this->total_amount;
    }
}