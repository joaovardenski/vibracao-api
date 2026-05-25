<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_lots', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_lots');
    }
};