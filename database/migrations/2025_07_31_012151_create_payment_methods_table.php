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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('description')->comment('Description of the payment method');
            $table->float('rate')->default(0)->comment('Rate applied to the payment method, if applicable');
            $table->boolean('is_active')->default(true)->comment('Indicates if the payment method is active');
            $table->foreignId('user_inserter_id')->nullable()->constrained('users')->onDelete('set null')->comment('User who created the payment method');
            $table->foreignId('user_updater_id')->nullable()->constrained('users')->onDelete('set null')->comment('User who last updated the payment method');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
