<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAffiliateFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('isAffiliate')->default(false);
            $table->unsignedBigInteger('affiliateId')->nullable();
            $table->timestamp('affiliatedAt')->nullable();
            $table->boolean('cpaCollected')->default(false);
            $table->timestamp('cpaCollectedAt')->nullable();

            // Adiciona a chave estrangeira para o afiliado (referenciando outro usuário)
            $table->foreign('affiliateId')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['affiliateId']);
            $table->dropColumn([
                'isAffiliate',
                'referrals',
                'affiliateId',
                'affiliatedAt',
                'cpaCollected',
                'cpaCollectedAt',
            ]);
        });
    }
}
