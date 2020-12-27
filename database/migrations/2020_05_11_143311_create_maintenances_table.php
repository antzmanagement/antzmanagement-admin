<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_id')->unsigned();
            $table->unsignedInteger('property_id')->unsigned();
            $table->unsignedInteger('owner_id')->unsigned()->nullable();
            $table->unsignedInteger('claim_id')->unsigned()->nullable();
            $table->string('uid');
            $table->string('name')->nullable();
            $table->decimal('price',8,2)->default(0.00);
            $table->longText('remark')->nullable();
            $table->string('maintenance_type')->default('repair');
            $table->string('maintenance_status')->default('pending');
            $table->boolean('isClaimed')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('room_id')
            ->references('id')
            ->on('rooms')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('property_id')
            ->references('id')
            ->on('properties')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('owner_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('claim_id')
            ->references('id')
            ->on('claims')
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
        Schema::dropIfExists('maintenances');
    }
}
