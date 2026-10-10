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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('provider_id');
            $table->string('service_slug'); // Matches ServiceDirectory slugs in Phase 1
            
            $table->enum('status', [
                'requested', 
                'accepted', 
                'rejected', 
                'in_progress', 
                'completed', 
                'cancelled_by_client', 
                'cancelled_by_provider', 
                'no_show_client', 
                'no_show_provider', 
                'disputed', 
                'resolved', 
                'refunded'
            ])->default('requested');
            
            $table->dateTime('scheduled_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
