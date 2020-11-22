<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio_snapshot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('position_id')->comment('Trading 212 ID for the position');
            $table->float('average_price');
            $table->float('average_price_converted');
            $table->float('current_price');
            $table->float('value')->comment('Total value of the investment now');
            $table->float('investment')->comment('Total initial buy in price');
            $table->float('margin');
            $table->float('ppl');
            $table->float('quantity');
            $table->boolean('active');
            $table->dateTime('last_held');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('portfolio_snapshot_id');
            $table->foreign('portfolio_snapshot_id')->references('id')->on('portfolio_snapshot');
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
        Schema::dropIfExists('positions');
        Schema::dropIfExists('portfolio_snapshot');
    }
}
