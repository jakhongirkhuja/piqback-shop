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
        Schema::connection('pgsql3')->table('course_histories', function (Blueprint $table) {
            $table->boolean('courseTypeByAccess')->default(1);
            $table->unsignedBigInteger('courseForGroup')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_histories', function (Blueprint $table) {
            //
        });
    }
};
