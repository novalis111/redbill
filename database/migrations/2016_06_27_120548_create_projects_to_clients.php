<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectsToClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'projects_to_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->char('interface_token');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('client_id');
            $table->timestamps();
        }
        );
        Schema::table(
            'projects_to_clients', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('companies')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['interface_token', 'project_id']);
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
        Schema::drop('projects_to_clients');
    }
}
