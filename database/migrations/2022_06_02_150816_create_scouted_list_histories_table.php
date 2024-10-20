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
        Schema::connection('pgsql2')->create('scouted_list_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scout_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('company_id');
            $table->index(['scout_id','group_id','company_id']);
            $table->string('status')->default('created');
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
        Schema::dropIfExists('scouted_list_histories');
    }
};
