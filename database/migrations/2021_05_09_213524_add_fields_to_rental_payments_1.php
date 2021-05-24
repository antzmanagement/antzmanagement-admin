<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToRentalPayments1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_payments', function (Blueprint $table) {
            $table->string('paymentmethod')->nullable();
            $table->string('receive_from')->nullable();
            $table->unsignedInteger('issueby')->unsigned()->nullable();
            //
            $table->foreign('issueby')
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
        Schema::table('rental_payments_1', function (Blueprint $table) {
            //
        });
    }
}
