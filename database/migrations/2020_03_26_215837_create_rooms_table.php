<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('uid')->unique();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('floor')->nullable();
            $table->string('jalan')->nullable();
            $table->string('unit')->nullable();
            $table->string('block')->nullable();
            $table->string('room_status')->default('empty');
            $table->decimal('price',8,2)->default(0.00);
            $table->decimal('size',8,2)->default(0.00);
            $table->boolean('sublet')->default(0);
            $table->decimal('sublet_claim',8,2)->default(0.00);
            $table->decimal('owner_claim',8,2)->default(0.00);
            $table->boolean('status')->default(1);
            $table->longText('remark')->nullable();
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
        Schema::dropIfExists('rooms');
    }
}
