<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomContractAddOnServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_contract_add_on_services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_contract_id')->unsigned();
            $table->unsignedInteger('service_id')->unsigned();
            $table->longText('remark')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('room_contract_id')
            ->references('id')
            ->on('room_contracts')
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
        Schema::dropIfExists('room_contract_add_on_services');
    }
}
