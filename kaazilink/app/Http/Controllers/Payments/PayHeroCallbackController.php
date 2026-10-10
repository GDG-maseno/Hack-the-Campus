<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PayHeroCallbackController extends Controller
{
    /**
     * Handle incoming PayHero transaction callback webhook.
     */
    public function __invoke(Request $request, PaymentManager $paymentManager): JsonResponse
    {
        $payload = $request->all();
        Log::info('[PayHero Callback] Webhook received', $payload);

        $driver = $paymentManager->driver('payhero');
        $result = $driver->handleCallback($payload);

        if (empty($result['reference'])) {
            Log::warning('[PayHero Callback] Missing reference in payload', $payload);
            return response()->json([
                'status' => 'error',
                'message' => 'Missing transaction reference in payload.'
            ], 400);
        }

        $payment = Payment::where('reference', $result['reference'])
            ->orWhere('checkout_request_id', $result['reference'])
            ->first();

        if (!$payment) {
            Log::warning("[PayHero Callback] Payment not found for reference [{$result['reference']}].");
            return response()->json([
                'status' => 'error',
                'message' => "Payment with reference [{$result['reference']}] not found."
            ], 404);
        }

        if ($result['success']) {
            $payment->update([
                'status' => 'successful',
                'transaction_id' => $result['transaction_id'] ?? $payment->transaction_id,
                'raw_callback_payload' => $payload,
                'paid_at' => now(),
                'error_message' => null,
            ]);

            // Update associated booking status to confirmed/accepted if applicable
            $booking = $payment->booking;
            if ($booking && in_array($booking->status, ['requested', 'pending'])) {
                $booking->update(['status' => 'accepted']);
            }
        } else {
            $status = ($result['status'] === 'cancelled') ? 'cancelled' : 'failed';
            $payment->update([
                'status' => $status,
                'error_message' => $result['message'] ?? 'Transaction failed or was cancelled by user.',
                'raw_callback_payload' => $payload,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Callback processed successfully.'
        ]);
    }
}
