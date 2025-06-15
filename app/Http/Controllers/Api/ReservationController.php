<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Order;
use App\Models\Product;
use App\Models\Cart;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;


class ReservationController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reservation_date' => 'required|date|after_or_equal:today',
                'reservation_time' => 'required|date_format:H:i',
                'people_count' => 'required|integer|min:1|max:20',
                'notes' => 'nullable|string|max:500',
                'payment_method' => 'required|in:credit_card,bank_transfer,gopay,shopeepay,qris'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            
            // Check if reservation time is within operating hours (8:00 - 22:00)
            $reservationTime = Carbon::createFromFormat('H:i', $request->reservation_time);
            $openingTime = Carbon::createFromFormat('H:i', '08:00');
            $closingTime = Carbon::createFromFormat('H:i', '22:00');

            if ($reservationTime->lt($openingTime) || $reservationTime->gt($closingTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation time must be between 08:00 and 22:00'
                ], 422);
            }

            // Get cart items
            $cartItems = Cart::where('user_id', Auth::id())
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Calculate total amount from cart items
            $totalAmount = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
            
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'reservation_number' => 'RES-' . Str::random(8),
                'reservation_date' => $request->reservation_date,
                'reservation_time' => $request->reservation_time,
                'people_count' => $request->people_count,
                'notes' => $request->notes,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'total_amount' => $totalAmount
            ]);

            // In store method
            $snapToken = $this->paymentService->createTransaction($reservation, $request->payment_method);

            // In createPayment method
            $snapToken = $this->paymentService->createTransaction($reservation, $reservation->payment_method);
            
            if (!$snapToken) {
                throw new \Exception('Failed to create payment transaction');
            }

            $reservation->update(['snap_token' => $snapToken]);

            // Clear the cart after successful reservation
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully',
                'data' => [
                    'reservation' => $reservation,
                    'reservation_number' => $reservation->reservation_number,
                    'snap_token' => $snapToken
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        try {
            $payload = $request->all();
            
            // Verify signature key
            $signatureKey = $payload['signature_key'] ?? null;
            $orderId = $payload['order_id'] ?? null;
            $statusCode = $payload['status_code'] ?? null;
            $grossAmount = $payload['gross_amount'] ?? null;
            $serverKey = config('midtrans.server_key');
            
            $mySignatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            
            if ($signatureKey !== $mySignatureKey) {
                throw new \Exception('Invalid signature');
            }
            
            if (!isset($payload['order_id']) || !isset($payload['transaction_status'])) {
                throw new \Exception('Invalid callback payload');
            }

            // Extract the actual reservation ID from the order_id (remove the prefix if any)
            $reservationId = str_replace('RES-', '', $payload['order_id']);
            $reservation = Reservation::where('reservation_number', $payload['order_id'])->firstOrFail();
            
            switch ($payload['transaction_status']) {
                case 'capture':
                case 'settlement':
                    $reservation->update([
                        'payment_status' => 'completed',
                        'status' => 'confirmed',
                        'midtrans_transaction_id' => $payload['transaction_id'],
                        'midtrans_payment_type' => $payload['payment_type'],
                        'midtrans_status_code' => $payload['status_code'],
                        'midtrans_status_message' => $payload['status_message'] ?? null
                    ]);
                    break;

                case 'pending':
                    $reservation->update([
                        'payment_status' => 'pending',
                        'midtrans_transaction_id' => $payload['transaction_id'],
                        'midtrans_payment_type' => $payload['payment_type'],
                        'midtrans_status_code' => $payload['status_code'],
                        'midtrans_status_message' => $payload['status_message'] ?? null
                    ]);
                    break;

                case 'deny':
                case 'expire':
                case 'cancel':
                    $reservation->update([
                        'payment_status' => 'failed',
                        'status' => 'cancelled',
                        'midtrans_transaction_id' => $payload['transaction_id'],
                        'midtrans_payment_type' => $payload['payment_type'],
                        'midtrans_status_code' => $payload['status_code'],
                        'midtrans_status_message' => $payload['status_message'] ?? null
                    ]);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment callback: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createPayment(Reservation $reservation)
    {
        try {
            if ($reservation->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this reservation'
                ], 403);
            }

            $snapToken = $this->paymentService->createTransaction($reservation, $reservation->payment_method);
            
            if (!$snapToken) {
                throw new \Exception('Failed to create payment transaction');
            }

            $reservation->update(['snap_token' => $snapToken]);

            return response()->json([
                'success' => true,
                'data' => [
                    'snap_token' => $snapToken
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ], 500);
        }
    }

public function getTimeSlots(Request $request)
{
    $today = Carbon::today();
    $timeSlots = [];

    // Define your time slots, for example, every hour from 8 AM to 10 PM
    $startTime = Carbon::createFromTime(8, 0);
    $endTime = Carbon::createFromTime(22, 0);

    while ($startTime->lessThan($endTime)) {
        $timeSlots[] = $startTime->format('H:i');
        $startTime->addHour();
    }

    return response()->json([
        'success' => true,
        'data' => [
            'date' => $today->toDateString(),
            'time_slots' => $timeSlots
        ]
    ]);
}
}