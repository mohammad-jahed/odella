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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('expiredSubscriptionDate')->nullable();
            $table->foreignId('transportation_line_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('transfer_position_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->nullable()->unique();
            $table->string('phoneNumber');
            $table->string('image')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('guestRequestStatus')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
