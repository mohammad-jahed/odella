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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('day_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transfer_position_id')->nullable()->constrained()->cascadeOnDelete();
            $table->time('start')->default("00:00:00");
            $table->time('end')->default("00:00:00");
            $table->boolean('confirmAttendance1')->default(true);
            $table->boolean('confirmAttendance2')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
