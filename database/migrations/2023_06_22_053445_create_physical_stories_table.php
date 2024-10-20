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
        Schema::connection('pgsql8')->create('physical_stories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('owner');
            $table->text('name')->unique();
            $table->string('longitude');
            $table->string('latitude');
            $table->string('landmark');
            $table->string('description');
            $table->bigInteger('mainContact')->nullable();
            $table->bigInteger('extraContact')->nullable();
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
        Schema::dropIfExists('physical_stories');
    }
};
