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
        Schema::table('provider_services', function (Blueprint $table) {
            $table->string('custom_name')->nullable();
            $table->text('custom_description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_custom')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_services', function (Blueprint $table) {
            $table->dropColumn([
                'custom_name',
                'custom_description',
                'price',
                'is_custom',
            ]);
        });
    }
};
