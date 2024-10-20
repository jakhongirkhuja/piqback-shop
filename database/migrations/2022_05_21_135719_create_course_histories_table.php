<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::connection('pgsql3')->create('course_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            
            $table->timestamp('startDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('endDate')->nullable();
            $table->boolean('courseMonetized')->default(0);
            $table->integer('coursePrice')->nullable();
            $table->string('courseType')->default('ongoing');
            $table->bigInteger('numberCustomers')->default(0);
            $table->bigInteger('numberGraduates')->default(0);
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
        Schema::dropIfExists('course_histories');
    }
};
