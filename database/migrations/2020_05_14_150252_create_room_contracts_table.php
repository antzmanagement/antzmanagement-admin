<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class CreateRoomContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_id')->unsigned();
            //Keep Track of room assigned to this contract
            $table->unsignedInteger('contract_id')->unsigned();
            $table->unsignedInteger('tenant_id')->unsigned();
            $table->string('uid');
            $table->string('name');
            $table->integer('duration')->default(0);
            $table->integer('leftmonth')->default(0);
            $table->integer('latestmonth')->default(0);
            $table->longText('terms')->nullable();
            $table->boolean('autorenew')->default(true);
            $table->date('startdate')->default(Carbon::now()->startOfMonth());
            $table->boolean('expired')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('room_id')
            ->references('id')
            ->on('rooms')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('contract_id')
            ->references('id')
            ->on('contracts')
            ->onUpdate('cascade')
            ->onDelete('restrict');
            
            $table->foreign('tenant_id')
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
        Schema::dropIfExists('room_contracts');
    }
}