<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('uid');
            $table->unsignedInteger('module_id')->unsigned();
            $table->string('name');
            $table->longText('desc')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            
            $table->foreign('module_id')
            ->references('id')
            ->on('modules')
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
        Schema::dropIfExists('actions');
    }
}
