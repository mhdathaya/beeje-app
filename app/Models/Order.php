<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        
        'order_number',
        'total_amount',
        'status',
        'payment_method',
        'order_method',
        'payment_status',
        'transaction_id',
        'payment_token',
        'payment_url',
        'transaction_status',
        'payment_type',
        'payment_time'
    ];

    const PAYMENT_METHODS = [
        'credit_card',
        'bank_transfer',
        'gopay',
        'shopeepay',
        'qris'
    ];

    const ORDER_METHODS = [
        'delivery',
        'reservation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function deliveryOrder()
    {
        return $this->hasOne(DeliveryOrder::class);
    }
    
    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }
}