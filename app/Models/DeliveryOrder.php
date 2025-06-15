<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipping_address',
        'delivery_notes',
        'origin_city',
        'destination_city',
        'courier',
        'service',
        'shipping_cost',
        'province_id',
        'city_id',
        'status',
        'tracking_info'
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'province_id' => 'integer',
        'city_id' => 'integer',
        'tracking_info' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}