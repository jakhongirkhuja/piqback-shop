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
        Schema::connection('pgsql10')->create('lottery_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lottery_id');
            $table->unsignedBigInteger('course_id');
            $table->timestamp('startDate');
            $table->timestamp('endDate');
            $table->integer('limit')->default(1);
            $table->text('name');
            $table->text('description');
            $table->timestamp('deadline');
            $table->unsignedBigInteger('editor')->nullable();
            $table->text('status')->default('created');
            $table->index(['lottery_id', 'course_id']);
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
        Schema::connection('pgsql10')->dropIfExists('lottery_histories');
    }
};
