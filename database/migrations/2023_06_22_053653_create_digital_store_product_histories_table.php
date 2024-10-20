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
        Schema::connection('pgsql8')->create('digital_store_product_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('store_id');
            $table->text('name');
            $table->text('img');
            $table->bigInteger('price');
            $table->bigInteger('amount')->default(1);
            $table->bigInteger('ordered')->default(0);
            
            $table->string('status',190)->default('created');
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
        Schema::dropIfExists('digital_store_product_histories');
    }
};
