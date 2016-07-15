<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id', false, true);
            $table->integer('client_id', false, true);
            $table->char('number');
            $table->char('title');
            $table->char('status', 25);
            $table->date('date_ordered');
            $table->date('date_delivered');
            $table->date('date_billed');
            $table->date('date_payed');
            $table->timestamps();
            $table->foreign('owner_id')->references('id')->on('companies')->onUpdate('cascade');
            $table->foreign('client_id')->references('id')->on('companies')->onUpdate('cascade');
            $table->unique(['number'], 'UNQ_INVOICE_NUMBER');
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('invoices');
    }
}
