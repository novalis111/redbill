<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'invoice_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('asset_id');
            $table->char('title');
            $table->float('amount');
            $table->float('price');
            $table->float('tax_rate');
            $table->timestamps();
        }
        );
        Schema::table(
            'invoice_entries', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('invoices')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('asset_id')->references('id')->on('assets')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->index('invoice_id', 'IDX_INVOICE_ID');
            $table->index('asset_id', 'IDX_ASSET_ID');
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
        Schema::drop('invoice_entries');
    }
}
