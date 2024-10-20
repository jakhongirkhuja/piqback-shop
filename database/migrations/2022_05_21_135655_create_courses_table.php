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
        Schema::connection('pgsql3')->create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->timestamp('startDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('endDate')->nullable();
            $table->boolean('courseMonetized')->default(0);
            $table->integer('coursePrice')->nullable();
            $table->string('courseType')->default('ongoing');
            $table->bigInteger('numberCustomers')->default(0);
            $table->bigInteger('numberGraduates')->default(0);
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
        Schema::dropIfExists('courses');
    }
};
