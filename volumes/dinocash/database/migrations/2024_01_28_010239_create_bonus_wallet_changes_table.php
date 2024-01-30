<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bonus_wallet_changes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bonusCampaignId');
            $table->decimal('amountOld', 10, 2);
            $table->decimal('amountNew', 10, 2);

            $table->timestamps();

            $table->foreign('bonusCampaignId')
                ->references('id')->on('bonus_campaigns');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bonus_wallet_changes');
    }
};
