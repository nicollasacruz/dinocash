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
        Schema::create('affiliate_invoices', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2)->default(0);
            $table->unsignedBigInteger('affiliateId');
            $table->string('status')->default('open');
            $table->dateTime('invoicedAt')->nullable();
            $table->dateTime('payedAt')->nullable();
            $table->timestamps();

            $table->foreign('affiliateId')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_invoices');
    }
};
