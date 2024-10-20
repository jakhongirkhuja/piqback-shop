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
        Schema::create('user_bio_histoires', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('users');
            $table->string('firstName',190);
            $table->string('lastName',190);
            $table->boolean('gender');
            $table->timestamp('birthDate');
            $table->bigInteger('hrid');
            $table->string('role')->nullable();
            $table->index('hrid');
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
        Schema::dropIfExists('user_bio_histoires');
    }
};
