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
        Schema::create('bonus_campaigns', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->decimal('amountMovement', 10, 2);
            $table->decimal('bonusPercent', 10, 2)->nullable();
            $table->decimal('rollover', 4, 2);
            $table->unsignedBigInteger('userId');
            $table->string('type');
            $table->string('status')->default('active');
            $table->timestamp('expireAt');
            $table->timestamps();

            $table->foreign('userId')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_campaigns');
    }
};
