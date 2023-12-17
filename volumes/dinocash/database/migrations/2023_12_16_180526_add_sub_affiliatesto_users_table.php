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
        Schema::table('users', function (Blueprint $table) {
            $table->string('revSub')->default(0)->after('CPA');
            $table->string('revSubFake')->default(0)->after('revSub');
            $table->string('cpaSub')->default(0)->after('revSubFake');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('revSub');
            $table->dropColumn('revSubFake');
            $table->dropColumn('cpaSub');
        });
    }
};
