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
            $table->integer('payout')->default(70);
            $table->integer('minWithdraw')->default(100);
            $table->integer('maxWithdraw')->default(1000);
            $table->integer('minDeposit')->default(10);
            $table->integer('maxDeposit')->default(5000);
            $table->integer('rollover')->default(2);
            $table->integer('defaultCPA')->default(20);
            $table->integer('defaultRevShare')->default(20);
            $table->boolean('autoPayWithdraw')->default(false);
            $table->integer('maxAutoPayWithdraw')->default(500);
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
