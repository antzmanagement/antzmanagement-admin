<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->increments('id')->unique();
            $table->unsignedInteger('role_id')->unsigned();
            $table->string('uid')->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('icno')->nullable();
            $table->string('tel1')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_tel')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_tel')->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_relationship')->nullable();
            $table->string('profile_img')->nullable();
            $table->string('profile_img_publicid')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('password')->nullable();
            $table->string('occupation')->nullable();
            $table->string('gender')->nullable()->default('male');
            $table->string('religion')->nullable();
            $table->string('approach_method')->nullable();
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->boolean('status')->default(1);
            $table->dateTime('birthday')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->dateTime('last_active')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('role_id')
            ->references('id')
            ->on('roles')
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
        Schema::dropIfExists('users');
    }
}
