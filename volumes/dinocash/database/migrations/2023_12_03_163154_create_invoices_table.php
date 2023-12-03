<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('amountPayed', 15, 2)->default(0);
            $table->string('status')->default('open');
            $table->dateTime('invoicedAt')->nullable();
            $table->dateTime('payedAt')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}

