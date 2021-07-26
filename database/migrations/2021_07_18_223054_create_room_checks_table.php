<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_id')->unsigned()->nullable();
            $table->string('uid');
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->longText('remark')->nullable();
            $table->date('checked_date')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('room_id')
            ->references('id')
            ->on('rooms')
            ->onUpdate('cascade')
            ->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_checks');
    }
}
