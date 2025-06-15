<?php

namespace App\Services;

use App\Models\Order;
use App\Models\DeliveryOrder;
use App\Models\Reservation; // Import Reservation model
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Import DB facade

class OrderService
{
    public function createDeliveryOrder($userId, $cartItems, $shippingData)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'DEL-' . Str::random(8),
                'total_amount' => $this->calculateTotal($cartItems) + ($shippingData['shipping_cost'] ?? 0),
                'status' => 'pending',
                'payment_method' => $shippingData['payment_method'],
                'order_method' => 'delivery',
                'payment_status' => 'pending'
            ]);

            foreach ($cartItems as $item) {
                // Check if there's enough stock
                if ($item->product->stock < $item->quantity) {
                    throw new \Exception("Not enough stock for product: {$item->product->name}");
                }

                // Reduce the stock
                $item->product->decrement('stock', $item->quantity);

                $order->products()->attach($item->product_id, [
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);
            }

            if ($shippingData['order_method'] === 'delivery') {
                DeliveryOrder::create([
                    'order_id' => $order->id,
                    'shipping_address' => $shippingData['shipping_address'],
                    'recipient_name' => $shippingData['recipient_name'],
                    'recipient_phone' => $shippingData['recipient_phone'],
                    'delivery_notes' => $shippingData['delivery_notes'] ?? null,
                    'origin_city' => $shippingData['origin_city'] ?? null,
                    'destination_city' => $shippingData['destination_city'] ?? null,
                    'courier' => $shippingData['courier'] ?? null,
                    'service' => $shippingData['service'] ?? null,
                    'shipping_cost' => $shippingData['shipping_cost'] ?? 0
                ]);
            }

            DB::commit();
            return $order->load(['products', 'deliveryOrder']);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function createReservationOrder($userId, $cartItems, $reservationData)
    {
        DB::beginTransaction();
        try {
            // Create the order first
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'RES-' . Str::random(8),
                'total_amount' => $this->calculateTotal($cartItems),
                'status' => 'pending',
                'payment_method' => $reservationData['payment_method'],
                'order_method' => 'reservation',
                'payment_status' => 'pending'
            ]);

            // Then create the reservation with the order_id
            $reservation = Reservation::create([
                'user_id' => $userId,
                'order_id' => $order->id, // Set the order_id immediately
                'reservation_date' => $reservationData['reservation_date'],
                'reservation_time' => $reservationData['reservation_time'],
                'people_count' => $reservationData['number_of_people'],
                'payment_method' => $reservationData['payment_method'],
                'total_amount' => $this->calculateTotal($cartItems),
                'status' => 'pending',
                'reservation_number' => 'RES-' . Str::random(8)
            ]);

            foreach ($cartItems as $item) {
                // Check if there's enough stock
                if ($item->product->stock < $item->quantity) {
                    throw new \Exception("Not enough stock for product: {$item->product->name}");
                }

                // Reduce the stock
                $item->product->decrement('stock', $item->quantity);

                $order->products()->attach($item->product_id, [
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);
            }

            DB::commit();
            return $order->load(['products', 'reservation']);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function calculateTotal($cartItems)
    {
        return collect($cartItems)->sum(function ($item) {
            // Ensure product relationship is loaded or fetch price
            $price = $item->product ? $item->product->price : \App\Models\Product::find($item->product_id)->price;
            return $price * $item->quantity;
        });
    }
}