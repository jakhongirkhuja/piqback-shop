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
        Schema::connection('pgsql10')->create('lottery_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lottery_id');
            $table->unsignedBigInteger('user_id');
            $table->text('ticket');
            $table->timestamp('datetime');
            $table->bigInteger('order');
            $table->index(['lottery_id','user_id']);
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
        Schema::connection('pgsql10')->dropIfExists('lottery_logs');
    }
};
