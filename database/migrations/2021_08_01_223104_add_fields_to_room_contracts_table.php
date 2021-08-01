<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToRoomContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('room_contracts', function (Blueprint $table) {
            $table->unsignedInteger('pic')->unsigned()->nullable();
            $table->decimal('outstanding',8,2)->default(0.00);

            $table->foreign('pic')
            ->references('id')
            ->on('users')
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
        Schema::table('room_contracts', function (Blueprint $table) {
            //
        });
    }
}
