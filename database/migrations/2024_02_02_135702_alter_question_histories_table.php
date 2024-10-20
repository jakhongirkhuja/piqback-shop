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
        Schema::connection('pgsql3')->table('question_histories', function (Blueprint $table) {
            $table->text('questionIMG')->nullable();
            $table->text('questionType')->default('single');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pgsql3')->table('question_histories', function (Blueprint $table) {
            //
        });
    }
};
