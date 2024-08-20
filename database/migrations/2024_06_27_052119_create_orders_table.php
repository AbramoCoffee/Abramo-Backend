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
            $table->string('invoice');
            $table->string('konsumen');
            $table->string('cashier');
            // $table->foreignId('cashier_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('payment_method');
            $table->integer('subtotal')->nullable();
            $table->integer('total_price');
            $table->integer('total_paid');
            $table->integer('total_return');
            $table->integer('tax')->nullable();
            $table->enum('status', ['proses', 'selesai']);
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
