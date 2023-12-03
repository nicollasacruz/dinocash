<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGgrTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('ggr_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->dateTime('invoicedAt');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ggr_transactions');
    }
}

