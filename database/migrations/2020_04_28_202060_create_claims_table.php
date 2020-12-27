<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {            
            $table->increments('id');
            $table->string('uid')->unique();
            $table->unsignedInteger('owner_id')->unsigned();
            $table->decimal('maintenance_fees',8,2)->default(0.00);
            $table->decimal('rental_fees',8,2)->default(0.00);
            $table->decimal('admin_fees',8,2)->default(0.00);
            $table->decimal('other_fees',8,2)->default(0.00);
            $table->longText('remark')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('owner_id')
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
        Schema::dropIfExists('claims');
    }
}
