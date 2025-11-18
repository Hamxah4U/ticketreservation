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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('qr_payload')->nullable();
            $table->enum('status', ['valid', 'used'])->default('valid');
            $table->string('seat_number')->nullable();
            $table->string('qrcodesvg')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
