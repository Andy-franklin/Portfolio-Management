<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->string('mic'); //Market Identifier Codes
            $table->string('name');
            $table->string('operating_mic')->nullable();
            $table->string('acronym')->nullable();
            $table->string('creation_date')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('status')->nullable();
            $table->string('comment')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('exchange_id')->after('ticker')->nullable();
            $table->foreign('exchange_id')->references('id')->on('exchanges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchanges');
    }
}
