<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectorsAndIndustry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('primary_choice');
            $table->unsignedBigInteger('same_as')->nullable();
            $table->string('source');
            $table->timestamps();
        });

        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('primary_choice');
            $table->unsignedBigInteger('same_as')->nullable()->comment('The industry id that this is the same as. If multiple industry are the same the primary_choice will be displayed.');
            $table->string('source');
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
        Schema::dropIfExists('sectors');
        Schema::dropIfExists('industries');
    }
}
