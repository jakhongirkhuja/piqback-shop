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
        Schema::connection('pgsql7')->create('inbox_message_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inbox_message_id');
            $table->text('newsPage')->nullable();
            $table->text('messageIcon');
            $table->jsonb('titleName');
            $table->jsonb('descriptionText');
            $table->unsignedBigInteger('promocode_id')->nullable();
            $table->timestamp('startDate')->nullable();
            $table->timestamp('endDate')->nullable();
            $table->string('sentBy',190);
            $table->unsignedBigInteger('phonebook_id');
            $table->unsignedBigInteger('moderated');
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('inbox_message_histories');
    }
};
