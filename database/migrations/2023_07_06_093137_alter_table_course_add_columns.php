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
        Schema::connection('pgsql3')->table('courses', function (Blueprint $table) {
            $table->boolean('access')->default(false);
            $table->boolean('pin')->default(false);
            $table->bigInteger('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pgsql3')->table('courses', function (Blueprint $table) {
            //
        });
    }
};
