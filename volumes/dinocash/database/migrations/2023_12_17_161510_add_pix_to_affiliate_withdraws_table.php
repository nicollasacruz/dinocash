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
        Schema::table('affiliate_withdraws', function (Blueprint $table) {
            $table->string('pixKey')->nullable();
            $table->string('pixValue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_withdraws', function (Blueprint $table) {
            $table->dropColumn('pixKey');
            $table->dropColumn('pixValue');
        });
    }
};
