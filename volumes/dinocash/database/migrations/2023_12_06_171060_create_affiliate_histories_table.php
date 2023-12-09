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
        Schema::create('affiliate_histories', function (Blueprint $table) {
            $table->id();
            $table->string('amount');
            $table->unsignedBigInteger('gameId')->nullable();
            $table->unsignedBigInteger('affiliateInvoiceId');
            $table->unsignedBigInteger('affiliateId');
            $table->unsignedBigInteger('userId');
            $table->string('type');
            $table->dateTime('invoicedAt')->nullable();
            $table->dateTime('payedAt')->nullable();
            $table->timestamps();

            $table->foreign('affiliateInvoiceId')->references('id')->on('affiliate_invoices')->onDelete('set null');
            $table->foreign('gameId')->references('id')->on('game_histories')->onDelete('set null');
            $table->foreign('affiliateId')->references('id')->on('users')->onDelete('set null');
            $table->foreign('userId')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_histories');
    }
};
