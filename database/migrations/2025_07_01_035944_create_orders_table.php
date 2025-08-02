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
            $table->id('order_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('delivery_time')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->enum('order_type', ['dine_in', 'take_out', 'delivery'])->default('dine_in');
            $table->enum('status', ['pending', 'processing', 'completed','delivered', 'cancelled'])->default('pending');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
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
