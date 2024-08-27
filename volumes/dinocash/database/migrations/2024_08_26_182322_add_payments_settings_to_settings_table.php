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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('payment_service')->default('EZZEBANK');
            $table->string('suitpay_url')->nullable();
            $table->string('suitpay_ci')->nullable();
            $table->string('suitpay_cs')->nullable();
            $table->string('suitpay_url_webhook')->default('/callback');
            $table->string('ezzebank_url')->nullable();
            $table->string('ezzebank_ci')->nullable();
            $table->string('ezzebank_cs')->nullable();
            $table->string('ezzebank_url_webhook')->default('/callback');
            $table->string('ezzebank_signature_key')->nullable();
            $table->string('ezzebank_auth')->nullable();
            $table->string('bspay_url')->nullable();
            $table->string('bspay_ci')->nullable();
            $table->string('bspay_cs')->nullable();
            $table->string('bspay_url_webhook')->default('/callback');
            $table->string('game_mode')->default('afiliado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
