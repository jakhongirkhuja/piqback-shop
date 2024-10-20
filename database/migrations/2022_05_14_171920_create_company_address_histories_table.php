<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql2')->create('company_address_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            // $table->foreign('company_id')->references('id')->on('companies');
            $table->boolean('addressType');
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->string('addressline1')->nullable();
            $table->unsignedBigInteger('moderated');
            $table->boolean('status')->default(0);
            $table->string('addressIP',190)->nullable();
            $table->string('platform',190)->nullable();
            $table->string('device',190)->nullable();
            $table->string('browser',190)->nullable();
            $table->string('timeZone',190)->nullable();
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
        Schema::dropIfExists('company_address_histories');
    }
};
