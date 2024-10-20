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
        Schema::connection('pgsql8')->create('store_seller_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_code_id');
            $table->unsignedBigInteger('seller_id');
            $table->boolean('action')->default(0);
            $table->text('reportIMG')->nullable();
            $table->text('shortDescription')->nullable();
            $table->timestamp('dateTime')->useCurrent();
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
        Schema::dropIfExists('store_seller_reports');
    }
};
