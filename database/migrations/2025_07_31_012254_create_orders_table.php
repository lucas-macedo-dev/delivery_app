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

            $table->uuid('ifood_id')
                ->unique()
                ->nullable()
                ->default(null)
                ->comment('Unique identifier from iFood system');

            $table->integer('ifood_order_number')
                ->nullable()
                ->default(null)
                ->index()
                ->comment('Order number as provided by iFood');

            // $table->foreignId('customer_id')
            //     ->nullable()
            //     ->default(null)
            //     ->constrained('customers')
            //     ->onDelete('cascade')
            //     ->comment('Reference to the customer who placed the order');

            $table->dateTime('order_date')
                ->comment('Date when the order was placed');

            $table->decimal('total_amount_order', 12, 2)
                ->default(0)
                ->comment('Total amount paid by the customer');

            $table->decimal('total_amount_received', 12, 2)
                ->default(0)
                ->comment('Total amount to be received by our company');

            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])
                ->default('pending')
                ->comment('Current status of the order');

            // $table->foreignId('payment_method_id')
            //     ->nullable()
            //     ->default(null)
            //     ->constrained('payment_methods')
            //     ->onDelete('cascade')
            //     ->comment('Payment method used for the order');

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
