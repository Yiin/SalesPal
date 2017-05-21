<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVatChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vat_checks', function (Blueprint $table) {
            $table->increments('id');

            $table->string('client_id');
            $table->string('address');
            $table->string('country_code');
            $table->string('name');
            $table->string('vat_number');
            $table->boolean('is_valid');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vat_checks');
    }
}
