<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnersRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('owners_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unsigned();
            $table->unsignedInteger('room_id')->unsigned();
            $table->text('remark')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');

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
        Schema::dropIfExists('owners_rooms');
    }
}
