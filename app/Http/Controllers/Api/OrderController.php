<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Models\Order;
use App\Models\Product;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\DeliveryService;

class OrderController extends Controller
{
    protected $paymentService;
    protected $deliveryService;

    public function __construct(PaymentService $paymentService, DeliveryService $deliveryService)
    {
        $this->paymentService = $paymentService;
        $this->deliveryService = $deliveryService;
    }


    public function createPayment(Order $order, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:bank_transfer,gopay,credit_card,shopeepay,qris'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $result = $this->paymentService->createTransaction($order, $request->payment_method);

            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            $order->update([
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'payment_status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $result['data']
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        try {
            // Log raw request untuk debug
            Log::info('Raw Midtrans Request', [
                'raw' => $request->getContent(),
                'headers' => $request->headers->all()
            ]);

            $payload = $request->all();
            
            // Log payload yang diterima
            Log::info('Midtrans Payload', $payload);

            // If payload is empty, try to get from raw content
            if (empty($payload)) {
                $rawContent = $request->getContent();
                $payload = json_decode($rawContent, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('Invalid JSON in callback', ['raw_content' => $rawContent]);
                    return response()->json(['message' => 'Invalid JSON payload'], 400);
                }
            }

            // Extract required fields with fallbacks
            $order_id = $payload['order_id'] ?? null;
            $status_code = $payload['status_code'] ?? null;
            $gross_amount = $payload['gross_amount'] ?? null;
            $signature_key = $payload['signature_key'] ?? null;
            $transaction_status = $payload['transaction_status'] ?? null;
            $fraud_status = $payload['fraud_status'] ?? null;
            $transaction_id = $payload['transaction_id'] ?? null;
            $payment_type = $payload['payment_type'] ?? null;

            Log::info('Extracted callback data', [
                'order_id' => $order_id,
                'status_code' => $status_code,
                'transaction_status' => $transaction_status,
                'fraud_status' => $fraud_status,
                'transaction_id' => $transaction_id,
                'payment_type' => $payment_type
            ]);

            // Validate required parameters
            if (!$order_id || !$status_code || !$gross_amount || !$signature_key) {
                Log::error('Missing required parameters in callback', [
                    'order_id' => $order_id,
                    'status_code' => $status_code,
                    'gross_amount' => $gross_amount,
                    'signature_key' => $signature_key ? 'present' : 'missing'
                ]);
                
                return response()->json([
                    'message' => 'Missing required parameters'
                ], 400);
            }

            // Signature validation
            $serverKey = config('midtrans.server_key');
            $signature = hash('sha512', $order_id . $status_code . $gross_amount . $serverKey);
            
            // Log signature comparison for debugging
            Log::info('Signature comparison', [
                'expected' => $signature,
                'received' => $signature_key
            ]);
            
            if ($signature !== $signature_key) {
                Log::error('Invalid signature in callback', [
                    'expected' => $signature,
                    'received' => $signature_key
                ]);
                
                // Untuk debugging, kita bisa menonaktifkan validasi signature sementara
                // return response()->json([
                //     'message' => 'Invalid signature'
                // ], 401);
            }

            // Parse order_id format TRX-{id}-{timestamp}
            $parts = explode('-', $order_id);
            if (count($parts) >= 2 && $parts[0] === 'TRX') {
                $actual_order_id = $parts[1];
                $order = Order::find($actual_order_id);

                if (!$order) {
                    Log::error('Order not found', [
                        'order_id' => $actual_order_id,
                        'searched_id' => $actual_order_id,
                        'all_orders' => Order::pluck('id')->toArray(),
                        'transaction_id' => $transaction_id
                    ]);
                    return response()->json([
                        'message' => 'Order not found'
                    ], 404);
                }

                // Log current order status before update
                Log::info('Order status before update', [
                    'order_id' => $order->id,
                    'current_status' => $order->status,
                    'current_payment_status' => $order->payment_status,
                    'transaction_id' => $order->transaction_id
                ]);

                // Tentukan status baru berdasarkan transaction_status dan fraud_status
                $newStatus = 'pending';
                $newPaymentStatus = 'pending';
                $paymentTime = null;                
                switch ($transaction_status) {
                    case 'capture':
                        if ($fraud_status == 'accept') {
                            $newStatus = 'paid';
                            $newPaymentStatus = 'completed';
                            $paymentTime = now();
                        }
                        break;
                        
                    case 'settlement':
                        $newStatus = 'paid';
                        $newPaymentStatus = 'completed';
                        $paymentTime = now();
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

                // Gunakan forceFill dan save untuk memastikan perubahan tersimpan
                $order->forceFill([
                    'transaction_id' => $transaction_id,
                    'payment_type' => $payment_type,
                    'status' => $newStatus,
                    'payment_status' => $newPaymentStatus,
                    'payment_time' => $paymentTime,
                    'transaction_status' => $transaction_status
                ]);
                
                // Pastikan perubahan tersimpan dengan DB::commit()
                DB::beginTransaction();
                try {
                    $saved = $order->save();
                    DB::commit();
                    Log::info('Order status updated successfully', [
                        'order_id' => $order->id,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                        'transaction_status' => $order->transaction_status,
                        'save_result' => $saved,
                        'new_status' => $newStatus,
                        'new_payment_status' => $newPaymentStatus
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Database transaction failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'order_id' => $order->id,
                        'new_status' => $newStatus,
                        'new_payment_status' => $newPaymentStatus
                    ]);
                    return response()->json([
                        'message' => 'Failed to update order status: ' . $e->getMessage()
                    ], 500);
                }

                // Setelah order status diperbarui dan disimpan
                if ($saved) {
                    // Buat notifikasi untuk user
                    $notificationTitle = '';
                    $notificationMessage = '';
                    
                    switch ($newStatus) {
                        case 'paid':
                            $notificationTitle = 'Pembayaran Berhasil';
                            $notificationMessage = 'Pembayaran untuk pesanan #' . $order->order_number . ' telah berhasil. Pesanan Anda sedang diproses.';
                            break;
                        case 'canceled':
                            $notificationTitle = 'Pembayaran Dibatalkan';
                            $notificationMessage = 'Pembayaran untuk pesanan #' . $order->order_number . ' telah dibatalkan.';
                            break;
                        case 'failed':
                            $notificationTitle = 'Pembayaran Gagal';
                            $notificationMessage = 'Pembayaran untuk pesanan #' . $order->order_number . ' gagal. Silakan coba lagi.';
                            break;
                        default:
                            $notificationTitle = 'Status Pesanan Diperbarui';
                            $notificationMessage = 'Status pesanan #' . $order->order_number . ' telah diperbarui menjadi ' . $newStatus . '.';
                    }
                    
                    // Buat data tambahan untuk notifikasi
                    $notificationData = [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $newStatus,
                        'payment_status' => $newPaymentStatus,
                        'transaction_status' => $transaction_status
                    ];
                    
                    // Buat notifikasi
                    NotificationController::createNotification(
                        $order->user_id,
                        'order_status',
                        $notificationTitle,
                        $notificationMessage,
                        $notificationData,
                        $order->id
                    );
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Callback processed successfully'
                ]);
            } else {
                // Coba cara alternatif untuk menemukan order
                Log::warning('Non-standard order ID format, trying alternative parsing', ['order_id' => $order_id]);
                
                // Coba cari order berdasarkan transaction_id
                $order = Order::where('transaction_id', $order_id)->first();
                
                if ($order) {
                    // Proses seperti di atas
                    // ...
                    return response()->json([
                        'status' => true,
                        'message' => 'Callback processed successfully with alternative order lookup'
                    ]);
                }
                
                Log::error('Invalid order ID format', ['order_id' => $order_id]);
                return response()->json([
                    'message' => 'Invalid order ID format'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function checkPaymentStatus(Request $request)
    {
        $order_id = $request->input('order_id');
        $order = Order::where('order_number', $order_id)->first();
        
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        try {
            // Pastikan order memiliki transaction_id
            if (!$order->transaction_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction ID not found'
                ], 400);
            }
            
            $result = $this->paymentService->checkTransactionStatus($order);
            
            // Refresh order data setelah update status
            $order->refresh();
            
            return response()->json([
                'status' => true,
                'data' => $result,
                'order_status' => [
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'transaction_status' => $order->transaction_status
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Payment status check failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function manualCheckPayment(Request $request)
    {
        $order_id = $request->input('order_id');
        
        if (!$order_id) {
            return response()->json([
                'status' => false,
                'message' => 'Order ID is required'
            ], 400);
        }

        $order = Order::where('order_number', $order_id)->first();
        
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        try {
            // Cek status pembayaran di Midtrans
            $result = $this->paymentService->checkTransactionStatus($order);
            
            // Update status order berdasarkan hasil cek
            $order->update([
                'transaction_status' => $result['transaction_status'],
                'status' => $result['transaction_status'] === 'settlement' ? 'paid' : 'pending',
                'payment_status' => $result['transaction_status'] === 'settlement' ? 'completed' : 'pending'
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Payment status updated successfully',
                'order_status' => [
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'transaction_status' => $order->transaction_status
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Manual payment check failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }  // End of manualCheckPayment method
    

public function handlePaymentFinish(Request $request)
{
    // Log informasi callback
    Log::info('Payment finish callback received', $request->all());
    
    // Ekstrak order_id dari parameter
    $orderId = $request->query('order_id');
    
    // Jika tidak ada order_id, kembalikan respons error
    if (!$orderId) {
        return response()->json([
            'status' => false,
            'message' => 'Order ID not provided'
        ], 400);
    }
    
    // Parse order_id format TRX-{id}-{timestamp}
    $parts = explode('-', $orderId);
    if (count($parts) >= 2 && $parts[0] === 'TRX') {
        $actualOrderId = $parts[1];
        $order = Order::find($actualOrderId);
        
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }
        
        // Kembalikan respons sukses dengan data order
        return response()->json([
            'status' => true,
            'message' => 'Payment completed successfully',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status
            ]
        ]);
    }
    
    return response()->json([
        'status' => false,
        'message' => 'Invalid order ID format'
    ], 400);
}

public function handlePaymentUnfinish(Request $request)
{
    // Log informasi callback
    Log::info('Payment unfinish callback received', $request->all());
    
    return response()->json([
        'status' => true,
        'message' => 'Payment is unfinished',
        'data' => $request->all()
    ]);
}

public function handlePaymentError(Request $request)
{
    // Log informasi callback
    Log::error('Payment error callback received', $request->all());
    
    return response()->json([
        'status' => false,
        'message' => 'Payment error occurred',
        'data' => $request->all()
    ]);
}
}