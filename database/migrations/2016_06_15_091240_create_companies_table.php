<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'companies', function (Blueprint $table) {
            $table->increments('id');
            $table->char('company_name');
            $table->char('salutation');
            $table->char('name');
            $table->char('street');
            $table->char('street_number');
            $table->char('postcode');
            $table->char('city');
            $table->char('country');
            $table->char('telephone');
            $table->char('mobile');
            $table->char('fax');
            $table->char('email');
            $table->char('website');
            $table->char('tax_number');
            $table->char('iban');
            $table->char('bic');
            $table->char('bank_name');
            $table->decimal('hourly_rate');
            $table->timestamps();
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
        Schema::drop('companies');
    }
}
