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
        Schema::create('daily_reservations', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("phoneNumber");
            $table->foreignId('transfer_position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('guestRequestStatus')->default(0);
            $table->integer('seatsNumber');
            $table->string('fcm_token')->default("");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reservations');
    }
};
