<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Initiate an M-PESA STK Push for the given payment.
     *
     * @param Payment $payment
     * @return array Standardized result: ['success' => bool, 'reference' => string|null, 'checkout_request_id' => string|null, 'message' => string]
     */
    public function initiateStkPush(Payment $payment): array;

    /**
     * Process an incoming webhook callback payload from the payment gateway.
     *
     * @param array $payload
     * @return array Standardized result: ['success' => bool, 'status' => string, 'reference' => string|null, 'transaction_id' => string|null, 'amount' => float|null, 'message' => string]
     */
    public function handleCallback(array $payload): array;

    /**
     * Query the status of an existing payment transaction.
     *
     * @param Payment $payment
     * @return array Standardized result: ['status' => string, 'raw' => array]
     */
    public function queryTransaction(Payment $payment): array;
}
