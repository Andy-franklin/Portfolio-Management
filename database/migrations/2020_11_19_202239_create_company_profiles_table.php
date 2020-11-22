<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->integer('employees')->nullable();
            $table->string('name')->nullable();
            $table->string('source');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });

        Schema::create('company_profile_sector', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_profile_id');
            $table->unsignedBigInteger('sector_id');
            $table->foreign('company_profile_id')->references('id')->on('company_profiles');
            $table->foreign('sector_id')->references('id')->on('sectors');

        });

        Schema::create('company_profile_industry', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_profile_id');
            $table->unsignedBigInteger('industry_id');
            $table->foreign('company_profile_id')->references('id')->on('company_profiles');
            $table->foreign('industry_id')->references('id')->on('industries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_profile_sector');
        Schema::dropIfExists('company_profile_industry');
        Schema::dropIfExists('company_profiles');
    }
}
