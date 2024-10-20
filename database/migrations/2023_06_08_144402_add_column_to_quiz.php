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
        Schema::connection('pgsql3')->table('quizzs', function (Blueprint $table) {
            $table->text('type')->default('with reward');
            $table->bigInteger('prizeLimit')->nullable();
            $table->boolean('prizeStatus')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pgsql3')->table('quizzs', function (Blueprint $table) {
            //
        });
    }
};
