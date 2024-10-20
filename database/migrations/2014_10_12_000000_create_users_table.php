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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstName',190);
            $table->string('lastName',190);
            $table->boolean('gender');
            $table->timestamp('birthDate');
            $table->string('language',190)->default('ru');
            $table->string('role')->nullable();
            $table->bigInteger('hrid');
            $table->index('hrid');
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
        Schema::dropIfExists('users');
    }
};
