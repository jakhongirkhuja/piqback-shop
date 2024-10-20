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
        Schema::connection('pgsql8')->create('store_objects', function (Blueprint $table) {
            $table->id();
            $table->jsonb('objectName');
            
            $table->jsonb('objectDescription');
            $table->text('objectIMG');
            $table->integer('objectCost')->default(0);
            $table->integer('objectAmount')->default(0);
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
        Schema::dropIfExists('store_objects');
    }
};
