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
        
        Schema::connection('pgsql7')->create('inbox_messages', function (Blueprint $table) {
            $table->id();
            $table->text('newsPage')->nullable();
            $table->text('messageIcon');
            $table->jsonb('titleName');
            $table->jsonb('descriptionText');
            $table->unsignedBigInteger('promocode_id')->nullable();
            $table->timestamp('startDate')->nullable();
            $table->timestamp('endDate')->nullable();
            $table->string('sentBy',190);
            $table->unsignedBigInteger('phonebook_id');
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
        Schema::dropIfExists('inbox_messages');
    }
};
