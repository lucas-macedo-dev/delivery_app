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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description')->comment('Description of the expense');
            $table->decimal('value', 10, 2)->comment('Value of the expense');
            $table->date('expense_date')->comment('Date when the expense was launched');
            $table->foreignId('user_inserter_id')->nullable()->constrained('users')->onDelete('set null')->comment('User who created the expense record');
            $table->foreignId('user_updater_id')->nullable()->constrained('users')->onDelete('set null')->comment('User who last updated the expense record');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
