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
        //
        Schema::create('shared_positions', function (Blueprint $table) {
            $table->foreignId("transportation_line_id")->constrained()->cascadeOnDelete();
            $table->foreignId("transfer_position_id")->constrained()->cascadeOnDelete();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('shared_positions');
    }
};
