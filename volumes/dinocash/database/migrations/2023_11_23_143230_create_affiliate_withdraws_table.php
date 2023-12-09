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
        Schema::create('affiliate_withdraws', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId');
            $table->string('transactionId');
            $table->string('amount');
            $table->string('type')->default('pending');
            $table->datetime('approvedAt')->nullable();
            $table->datetime('reprovedAt')->nullable();
            $table->unsignedBigInteger('managerUserId')->nullable();
            $table->timestamps();

            $table->foreign('userId')
                ->references('id')->on('users');
            $table->foreign('managerUserId')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
