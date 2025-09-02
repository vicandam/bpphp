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
        Schema::create('ticket_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            $table->string('xendit_external_id')->unique()->comment('Corresponds to Xendit invoice external_id and ticket_code for Ticket model');
            $table->string('xendit_invoice_id')->nullable()->unique()->comment('Xendit invoice ID');
            $table->decimal('amount', 10, 2); // Store the amount for the request
            $table->string('status')->default('pending')->comment('pending, paid, failed, expired, cancelled');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_requests');
    }
};
