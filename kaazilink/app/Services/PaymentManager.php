<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\Payments\PayHeroDriver;
use InvalidArgumentException;

class PaymentManager
{
    /**
     * The registered gateway drivers.
     *
     * @var array<string, PaymentGatewayInterface>
     */
    protected array $drivers = [];

    public function __construct()
    {
        // Register default PayHero driver
        $this->registerDriver('payhero', app(PayHeroDriver::class));
    }

    /**
     * Register a payment gateway driver.
     */
    public function registerDriver(string $name, PaymentGatewayInterface $driver): self
    {
        $this->drivers[$name] = $driver;
        return $this;
    }

    /**
     * Get a payment gateway driver by name.
     */
    public function driver(?string $name = null): PaymentGatewayInterface
    {
        $gatewayName = $name ?: config('payments.default', 'payhero');

        if (!isset($this->drivers[$gatewayName])) {
            throw new InvalidArgumentException("Payment gateway driver [{$gatewayName}] is not supported.");
        }

        return $this->drivers[$gatewayName];
    }

    /**
     * Resolve the gateway for a booking payment.
     *
     * RETRY RULE: If an earlier payment attempt exists for this booking,
     * the retry MUST use the SAME gateway as the original attempt,
     * regardless of changes to the current system default config.
     *
     * GATEWAY LOCK-IN RULE: If no prior payment exists, uses current default.
     */
    public function resolveGatewayForBooking(Booking $booking): string
    {
        $existingPayment = $booking->payments()->latest()->first();

        if ($existingPayment && !empty($existingPayment->gateway)) {
            return $existingPayment->gateway;
        }

        return (string) config('payments.default', 'payhero');
    }

    /**
     * Initiate an M-PESA STK Push payment for a booking.
     *
     * Stores gateway directly on payment record at the moment of attempt.
     */
    public function initiateBookingPayment(Booking $booking, string $phone): Payment
    {
        if (empty($booking->price) || $booking->price <= 0) {
            throw new InvalidArgumentException("Cannot initiate payment: Booking has no valid agreed price.");
        }

        // 1. Resolve Gateway (enforcing Retry Rule if previously attempted)
        $gateway = $this->resolveGatewayForBooking($booking);

        // 2. Format Kenyan phone number
        $cleanPhone = $this->formatKenyanPhone($phone);

        // 3. Create Payment record (Gateway Lock-In Rule)
        $reference = 'KZL-' . strtoupper(uniqid());
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->client_id,
            'gateway' => $gateway,
            'amount' => $booking->price,
            'phone' => $cleanPhone,
            'status' => 'pending',
            'reference' => $reference,
        ]);

        // 4. Dispatch STK Push via the resolved gateway driver
        $driver = $this->driver($gateway);
        $result = $driver->initiateStkPush($payment);

        // 5. Update payment state based on initiation outcome
        if (!empty($result['success'])) {
            $payment->update([
                'status' => 'initiated',
                'checkout_request_id' => $result['checkout_request_id'] ?? null,
            ]);
        } else {
            $payment->update([
                'status' => 'failed',
                'error_message' => $result['message'] ?? 'Failed to initiate STK push.',
            ]);
        }

        return $payment;
    }

    /**
     * Format a Kenyan phone number to 254XXXXXXXXX standard format.
     */
    public function formatKenyanPhone(string $phone): string
    {
        $cleaned = preg_replace('/[^\d]/', '', $phone);

        if (str_starts_with($cleaned, '0') && strlen($cleaned) === 10) {
            return '254' . substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '254') && strlen($cleaned) === 12) {
            return $cleaned;
        }

        if (strlen($cleaned) === 9 && in_array($cleaned[0], ['7', '1'])) {
            return '254' . $cleaned;
        }

        return $cleaned;
    }
}
