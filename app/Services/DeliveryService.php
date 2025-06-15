<?php

namespace App\Services;

use App\Models\Order;
use App\Models\DeliveryOrder;
use App\Http\Controllers\Api\NotificationController;

class DeliveryService
{
    /**
     * Update delivery status and create notification
     */
    public function updateDeliveryStatus(Order $order, $status, $trackingInfo = null)
    {
        // Pastikan order memiliki delivery order
        if (!$order->deliveryOrder) {
            throw new \Exception('Order does not have delivery information');
        }
        
        // Update status pengiriman
        $order->deliveryOrder->update([
            'status' => $status,
            'tracking_info' => $trackingInfo
        ]);
        
        // Buat notifikasi berdasarkan status
        $notificationTitle = '';
        $notificationMessage = '';
        
        switch ($status) {
            case 'processing':
                $notificationTitle = 'Pesanan Diproses';
                $notificationMessage = 'Pesanan #' . $order->order_number . ' sedang diproses dan akan segera dikirim.';
                break;
            case 'shipped':
                $notificationTitle = 'Pesanan Dikirim';
                $notificationMessage = 'Pesanan #' . $order->order_number . ' telah dikirim dan sedang dalam perjalanan.';
                break;
            case 'delivered':
                $notificationTitle = 'Pesanan Diterima';
                $notificationMessage = 'Pesanan #' . $order->order_number . ' telah diterima. Terima kasih telah berbelanja!';
                break;
            case 'failed':
                $notificationTitle = 'Pengiriman Gagal';
                $notificationMessage = 'Pengiriman pesanan #' . $order->order_number . ' mengalami kendala. Kami akan menghubungi Anda segera.';
                break;
            default:
                $notificationTitle = 'Status Pengiriman Diperbarui';
                $notificationMessage = 'Status pengiriman pesanan #' . $order->order_number . ' telah diperbarui menjadi ' . $status . '.';
        }
        
        // Buat data tambahan untuk notifikasi
        $notificationData = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'delivery_status' => $status,
            'tracking_info' => $trackingInfo
        ];
        
        // Buat notifikasi
        return NotificationController::createNotification(
            $order->user_id,
            'delivery_status',
            $notificationTitle,
            $notificationMessage,
            $notificationData,
            $order->id
        );
    }
}