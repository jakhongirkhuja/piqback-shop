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
        Schema::connection('pgsql6')->create('register_bios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('addressIP');
            $table->string('firstName',190);  
            $table->string('lastName',190);
            $table->boolean('gender');
            $table->timestamp('birthDate');
            $table->string('language',190)->default('ru');
            $table->string('platform',190)->nullable();
            $table->string('device',190)->nullable();
            $table->string('browser',190)->nullable();
            $table->string('timeZone',190)->nullable();
            $table->index(['addressIP']);
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
        Schema::dropIfExists('register_bios');
    }
};
