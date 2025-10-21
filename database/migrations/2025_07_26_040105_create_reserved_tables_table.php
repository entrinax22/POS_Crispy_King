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
        Schema::create('reserved_tables', function (Blueprint $table) {
            $table->id('reserved_table_id');
            $table->unsignedBigInteger('table_id');
            $table->unsignedBigInteger('reserved_by')->nullable();
            $table->text('name')->nullable();
            $table->dateTime('reservation_time');
            $table->foreign('table_id')->references('table_id')->on('tables')->onDelete('cascade');
            $table->foreign('reserved_by')->references('user_id')->on('users')->onDelete('set null');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserved_tables');
    }
};
