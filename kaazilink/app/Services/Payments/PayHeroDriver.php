<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PayHeroDriver implements PaymentGatewayInterface
{
    protected string $apiUsername;
    protected string $apiPassword;
    protected string $channelId;
    protected string $callbackUrl;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiUsername = (string) config('payments.gateways.payhero.api_username', '');
        $this->apiPassword = (string) config('payments.gateways.payhero.api_password', '');
        $this->channelId = (string) config('payments.gateways.payhero.channel_id', '');
        $this->callbackUrl = (string) config('payments.gateways.payhero.callback_url', '');
        $this->baseUrl = (string) config('payments.gateways.payhero.base_url', 'https://backend.payhero.co.ke/api/v2');
    }

    /**
     * Initiate an M-PESA STK Push via PayHero.
     */
    public function initiateStkPush(Payment $payment): array
    {
        $this->ensureSdkLoaded();

        if (empty($this->apiUsername) || empty($this->apiPassword)) {
            Log::warning("[PayHero] Missing credentials in configuration.");
            return [
                'success' => false,
                'checkout_request_id' => null,
                'reference' => $payment->reference,
                'message' => 'PayHero API credentials are not configured.',
                'raw' => [],
            ];
        }

        try {
            $api = new \PayHeroAPI($this->apiUsername, $this->apiPassword);
            $responseRaw = $api->SendCustomerMpesaStkPush(
                (int) round($payment->amount),
                $payment->phone,
                $this->channelId,
                $payment->reference,
                $this->callbackUrl
            );

            $response = json_decode($responseRaw, true) ?: [];

            $isSuccess = ($response['status'] ?? false) === 'Success' 
                || ($response['success'] ?? false) === true 
                || isset($response['CheckoutRequestID'])
                || isset($response['reference']);

            return [
                'success' => $isSuccess,
                'checkout_request_id' => $response['CheckoutRequestID'] ?? $response['reference'] ?? null,
                'reference' => $payment->reference,
                'message' => $response['message'] ?? ($isSuccess ? 'STK Push initiated successfully' : 'STK Push failed to initiate'),
                'raw' => $response,
            ];
        } catch (\Throwable $e) {
            Log::error("[PayHero] Exception initiating STK Push: " . $e->getMessage());
            return [
                'success' => false,
                'checkout_request_id' => null,
                'reference' => $payment->reference,
                'message' => $e->getMessage(),
                'raw' => [],
            ];
        }
    }

    /**
     * Process an incoming webhook callback payload from PayHero.
     */
    public function handleCallback(array $payload): array
    {
        Log::info("[PayHero] Callback received: ", $payload);

        // Support PayHero payload formats (nested response object or flat)
        $data = $payload['response'] ?? $payload;

        $statusStr = (string)($data['Status'] ?? $data['status'] ?? $payload['Status'] ?? $payload['status'] ?? '');
        $statusBool = ($data['status'] ?? $payload['status'] ?? null) === true;
        $resultCode = $data['ResultCode'] ?? $payload['ResultCode'] ?? null;
        $resultDesc = $data['ResultDesc'] ?? $data['result_desc'] ?? $data['message'] ?? $payload['message'] ?? null;

        $isSuccess = strcasecmp($statusStr, 'Success') === 0 
            || $statusBool === true
            || ($data['success'] ?? false) === true
            || ($resultCode === 0 || $resultCode === '0' && $resultCode !== null);

        $isCancelled = false;
        if ((int)$resultCode === 1032 || ($resultDesc && stripos((string)$resultDesc, 'cancel') !== false)) {
            $isCancelled = true;
        }

        $reference = $data['User_Reference']
            ?? $data['user_reference']
            ?? $data['external_reference'] 
            ?? $data['ExternalReference'] 
            ?? $data['reference'] 
            ?? $data['Reference'] 
            ?? $data['CheckoutRequestID'] 
            ?? $data['checkout_request_id'] 
            ?? $payload['User_Reference'] 
            ?? $payload['user_reference'] 
            ?? $payload['external_reference'] 
            ?? $payload['ExternalReference'] 
            ?? $payload['reference'] 
            ?? $payload['Reference'] 
            ?? $payload['CheckoutRequestID'] 
            ?? null;

        $transactionId = $data['MPESA_Reference'] 
            ?? $data['MpesaReceiptNumber'] 
            ?? $data['mpesa_reference'] 
            ?? $data['transaction_id'] 
            ?? $payload['MPESA_Reference'] 
            ?? $payload['MpesaReceiptNumber'] 
            ?? null;

        $amount = isset($data['Amount']) ? (float)$data['Amount'] : (isset($data['amount']) ? (float)$data['amount'] : null);

        if ($isSuccess) {
            $message = 'Payment completed successfully via M-PESA.';
            $status = 'successful';
        } elseif ($isCancelled) {
            $message = $resultDesc ?: 'Payment prompt was cancelled on phone.';
            $status = 'cancelled';
        } else {
            $message = $resultDesc ?: 'M-PESA payment failed or was declined.';
            $status = 'failed';
        }

        return [
            'success' => $isSuccess,
            'status' => $status,
            'reference' => $reference,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'message' => $message,
            'result_code' => $resultCode,
            'result_desc' => $resultDesc,
            'raw' => $payload,
        ];
    }

    /**
     * Query transaction status via PayHero API.
     */
    public function queryTransaction(Payment $payment): array
    {
        $this->ensureSdkLoaded();

        if (empty($this->apiUsername) || empty($this->apiPassword)) {
            return ['status' => 'unknown', 'raw' => []];
        }

        try {
            $api = new \PayHeroAPI($this->apiUsername, $this->apiPassword);
            $raw = $api->getTransactionStatus($payment->checkout_request_id ?: $payment->reference);
            $data = json_decode($raw, true) ?: [];
            return ['status' => $data['status'] ?? 'unknown', 'raw' => $data];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'raw' => ['error' => $e->getMessage()]];
        }
    }

    /**
     * Ensure third-party PayHero SDK is required if not autoloaded.
     */
    protected function ensureSdkLoaded(): void
    {
        if (!class_exists(\PayHeroAPI::class)) {
            $sdkPath = base_path('vendor/payherokenya/payhero-php/ph-class.php');
            if (file_exists($sdkPath)) {
                require_once $sdkPath;
            }
        }
    }
}
