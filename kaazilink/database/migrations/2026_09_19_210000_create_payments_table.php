<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('gateway'); // e.g. 'payhero', 'daraja' (Gateway Lock-in Rule)
            $table->decimal('amount', 10, 2);
            $table->string('phone');
            $table->string('status')->default('pending'); // pending, initiated, successful, failed, timeout
            $table->string('reference')->unique(); // Internal tracking reference
            $table->string('checkout_request_id')->nullable()->index(); // Gateway Checkout/Request ID
            $table->string('transaction_id')->nullable()->index(); // M-PESA Receipt Number
            $table->text('error_message')->nullable();
            $table->json('raw_callback_payload')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
