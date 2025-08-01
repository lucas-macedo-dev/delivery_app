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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->json('details')->comment('Order details including product IDs and quantities');
            $table->decimal('total_price', 10, 2)->comment('Total price of the order calculated as quantity * product price');
            $table->float('delivery_fee')->default(0)->comment('Delivery fee for the order');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade')->comment('Payment method used for the order');
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending')->comment('Current status of the order');
            $table->foreignId('user_inserter_id')->nullable()->constrained('users')->onDelete('set null')->comment('User who created the order');
            $table->foreignId('user_updater_id')->nullable()->constrained('users')->onDelete('set null')->comment('User who last updated the order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
