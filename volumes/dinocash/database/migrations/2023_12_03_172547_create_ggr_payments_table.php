<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGgrPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('ggr_payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->string('status')->default('pending');
            $table->dateTime('payedAt')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ggr_payments');
    }
}
