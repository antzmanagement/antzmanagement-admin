<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomTypesServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_types_services', function (Blueprint $table) {
            
            $table->increments('id');
            $table->unsignedInteger('room_type_id')->unsigned();
            $table->unsignedInteger('service_id')->unsigned();
            $table->longText('remark')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('room_type_id')
            ->references('id')
            ->on('room_types')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('service_id')
            ->references('id')
            ->on('services')
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
        Schema::dropIfExists('room_types_services');
    }
}
