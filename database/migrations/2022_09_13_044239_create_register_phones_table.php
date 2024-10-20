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
        Schema::connection('pgsql6')->create('register_phones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('addressIP');
            $table->unsignedBigInteger('phoneNumber');
            $table->integer('confirm_code')->nullable();
            $table->index(['addressIP','phoneNumber']);
            $table->string('language',190)->default('ru');
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
        Schema::dropIfExists('register_phones');
    }
};
