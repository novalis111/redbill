<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'assets', function (Blueprint $table) {
            $table->increments('id');
            $table->char('interface_token');
            $table->unsignedInteger('foreign_id');
            $table->unsignedInteger('client_id');
            $table->char('type');
            $table->char('title');
            $table->float('amount');
            $table->char('unit', 30);
            $table->date('delivery_date');
            $table->text('comment');
            $table->timestamps();
        }
        );
        Schema::table(
            'assets', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('companies')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->index('foreign_id', 'IDX_FOREIGN_ID');
            $table->index('client_id', 'IDX_CLIENT_ID');
            $table->index('type', 'IDX_TYPE');
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
        Schema::drop('assets');
    }
}
