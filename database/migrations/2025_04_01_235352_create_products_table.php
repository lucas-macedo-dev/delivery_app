<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->float('price');
            $table->boolean('available');
            $table->string('image_name');
            $table->integer('stock_quantity')->default(0);
            $table->integer('category');
            $table->string('unit_measure', 3)->default('un')->comment('un: unit, kg: kilogram, g: gram, l: liter,  ml: milliliter, m: meter, cm: centimeter, mm: millimeter');
            $table->timestamps();

            $table->foreign('category')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
