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
        Schema::connection('pgsql7')->create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('promocode',190);
            $table->boolean('prizeType')->default(0);
            $table->integer('prizeAmount');
            $table->timestamp('startDate')->nullable();
            $table->timestamp('endDate')->nullable();
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
        Schema::dropIfExists('promocodes');
    }
};
