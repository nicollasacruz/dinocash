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
            $table->integer('amount');
            $table->integer('finalAmount')->nullable();
            $table->string('type')->default('pendent');
            $table->unsignedBigInteger('distance')->default(0);
            $table->timestamps();

            // Ensure the foreign key is unsigned
            $table->foreign('userId')
                ->references('id')->on('users')
                ->onDelete('cascade');
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
