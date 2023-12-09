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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('emailFatura')->nullable();
            $table->integer('payout')->default(70);
            $table->integer('minWithdraw')->default(100);
            $table->integer('maxWithdraw')->default(1000);
            $table->integer('minAmountPlay')->default(1);
            $table->integer('maxAmountPlay')->default(1000);
            $table->integer('minDeposit')->default(10);
            $table->integer('maxDeposit')->default(10000);
            $table->integer('rollover')->default(2);
            $table->integer('defaultCPA')->default(20);
            $table->boolean('affiliatePayGGR')->default(true);
            $table->integer('defaultRevShare')->default(20);
            $table->boolean('autoPayWithdraw')->default(true);
            $table->integer('maxAutoPayWithdraw')->default(100);
            $table->timestamps();
        });

        DB::table('settings')->insert([
            'payout' => 70,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
