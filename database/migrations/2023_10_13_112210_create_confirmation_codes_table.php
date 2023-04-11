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
        Schema::create('confirmation_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()->cascadeOnDelete();
            $table->string('confirm_code');
            $table->tinyInteger('is_confirmed')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('confirmation_codes');
    }
};
