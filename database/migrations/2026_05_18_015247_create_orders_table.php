<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignUuid('ticket_lot_id')->constrained('ticket_lots');
            $table->string('ticket_number', 20)->nullable()->unique();
            $table->enum('status', ['pending', 'approved', 'expired', 'cancelled'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
