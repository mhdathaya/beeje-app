<?php

    namespace App\Services;

    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;

    class PaymentService
    {
        protected $serverKey;
        protected $isProduction;
        protected $isSanitized;
        protected $is3ds;
        protected $snapUrl; // Tambahkan deklarasi properti snapUrl di sini
        
        public function __construct()
        {
            $this->serverKey = config('midtrans.server_key');
            $this->isProduction = config('midtrans.is_production');
            $this->isSanitized = config('midtrans.is_sanitized',false);
            $this->snapUrl = $this->isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
        }

        public function createTransaction($order, $paymentMethod)
        {
            try {
                // Gunakan format yang konsisten untuk orderId
                $orderId = $order instanceof \App\Models\Reservation 
                    ? 'RES-' . $order->id . '-' . time()
                    : 'TRX-' . $order->id . '-' . time();
                  
                $baseUrl = config('app.url');
                $items = [];
                foreach ($order->products as $product) {
                    $items[] = [
                        'id' => $product->id,
                        'price' => $product->price,
                        'quantity' => $product->pivot->quantity,
                        'name' => $product->name,
                    ];
                }
                if ($order->order_method === 'delivery' && $order->deliveryOrder && $order->deliveryOrder->shipping_cost > 0) {
                    $items[] = [
                        'id' => 'shipping',
                        'price' => (int) $order->deliveryOrder->shipping_cost,
                        'quantity' => 1,
                        'name' => 'Shipping Cost (' . $order->deliveryOrder->courier . ' - ' . $order->deliveryOrder->service . ')',
                    ];
                }
                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $order->total_amount
                    ],
                    'customer_details' => [
                        'first_name' => $order->user->name,
                        'email' => $order->user->email,
                        'phone' => $order->user->phone ?? '',
                        'billing_address' => [
                            'address' => $order->address ?? '',
                        ],
                    ],
                    'item_details' => $items,
                    'enabled_payments' => $this->getEnabledPayments($paymentMethod),
                    // Tambahkan callback URLs
                    'callbacks' => [
                        'finish' => $baseUrl . '/payment/finish',
                        'unfinish' => $baseUrl . '/payment/unfinish',
                        'error' => $baseUrl . '/payment/error',
                    ],
                ];

                $auth = base64_encode($this->serverKey . ':');

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $auth,
                ])->post($this->snapUrl, $params); // PERBAIKAN: Gunakan $this->snapUrl bukan $baseUrl

                if ($response->failed()) {
                    throw new \Exception('Midtrans error: ' . $response->body());
                }

                $result = $response->json();
                Log::info('Midtrans response', $result);

                // Update order with payment details
                $order->update([
                    'payment_token' => $result['token'],
                    'payment_url' => $result['redirect_url'],
                    'transaction_id' => $orderId,
                ]);

                return [
                    'status' => true,
                    'data' => $result
                ];

            } catch (\Exception $e) {
                Log::error('Payment creation failed: ' . $e->getMessage());
                return [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        private function getEnabledPayments($method)
        {
            switch (strtolower($method)) {
                case 'bank_transfer':
                    return ['bank_transfer'];
                case 'gopay':
                    return ['gopay'];
                case 'credit_card':
                    return ['credit_card'];
                default:
                    return ['bank_transfer', 'gopay', 'credit_card', 'shopeepay', 'qris'];
            }
        }

        public function checkTransactionStatus($order)
        {
            try {
                // Pastikan kita menggunakan transaction_id yang benar
                $transactionId = $order->transaction_id;
                
                if (!$transactionId) {
                    throw new \Exception('Transaction ID not found');
                }
                
                $baseUrl = $this->isProduction
                    ? 'https://api.midtrans.com/v2/'
                    : 'https://api.sandbox.midtrans.com/v2/';

                $auth = base64_encode($this->serverKey . ':');

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $auth,
                ])->get($baseUrl . $transactionId . '/status');

                if ($response->successful()) {
                    $result = $response->json();
                    
                    // Update order status berdasarkan response
                    $this->updateOrderStatus($order, $result);
                    
                    return $result;
                }

                throw new \Exception('Failed to check transaction status: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Check transaction status failed: ' . $e->getMessage());
                return [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        // Tambahkan metode baru untuk memperbarui status order
        private function updateOrderStatus($order, $transactionData)
        {
            $transactionStatus = $transactionData['transaction_status'] ?? null;
            $fraudStatus = $transactionData['fraud_status'] ?? null;
            
            if (!$transactionStatus) {
                return;
            }
            
            // Tentukan status baru berdasarkan transaction_status dan fraud_status
            $newStatus = 'pending';
            $newPaymentStatus = 'pending';
            
            switch ($transactionStatus) {
                case 'capture':
                    if ($fraudStatus == 'accept') {
                        $newStatus = 'paid';
                        $newPaymentStatus = 'completed';
                    }
                    break;
                    
                case 'settlement':
                    $newStatus = 'paid';
                    $newPaymentStatus = 'completed';
                    break;
                    
                case 'pending':
                    $newStatus = 'pending';
                    $newPaymentStatus = 'pending';
                    break;
                    
                case 'deny':
                case 'expire':
                case 'cancel':
                    $newStatus = 'canceled';
                    $newPaymentStatus = 'failed';
                    break;
                    
                case 'failure':
                    $newStatus = 'failed';
                    $newPaymentStatus = 'failed';
                    break;
            }
            
            // Update status order dengan force update dan pastikan save() dipanggil
            $order->forceFill([
                'status' => $newStatus,
                'payment_status' => $newPaymentStatus,
                'transaction_status' => $transactionStatus,
                'payment_type' => $transactionData['payment_type'] ?? null,
                'payment_time' => ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) 
                    ? now() : null
            ]);
            $order->save();
            
            Log::info('Order status updated from transaction check', [
                'order_id' => $order->id,
                'new_status' => $newStatus,
                'new_payment_status' => $newPaymentStatus,
                'transaction_status' => $transactionStatus
            ]);
        }
    }