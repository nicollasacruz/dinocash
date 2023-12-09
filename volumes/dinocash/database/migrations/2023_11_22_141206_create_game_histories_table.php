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
        Schema::create('game_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId');
            $table->string('amount');
            $table->string('finalAmount')->nullable();
            $table->string('type')->default('pending');
            $table->unsignedBigInteger('distance')->default(0);
            $table->timestamps();

            // Ensure the foreign key is unsigned
            $table->foreign('userId')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_histories');
    }
};
