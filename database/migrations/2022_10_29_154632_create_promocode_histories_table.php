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
        Schema::connection('pgsql7')->create('promocode_histories', function (Blueprint $table) {
            $table->id();
            $table->string('promocode',190);
            $table->boolean('prizeType')->default(0);
            $table->integer('prizeAmount');
            $table->timestamp('startDate')->nullable();
            $table->timestamp('endDate')->nullable();
            $table->string('status',190)->default('created');
            $table->unsignedBigInteger('moderated');
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
        Schema::dropIfExists('promocode_histories');
    }
};
