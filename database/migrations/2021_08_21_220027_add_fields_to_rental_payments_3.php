<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToRentalPayments3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_payments', function (Blueprint $table) {
            $table->boolean('penaltyEdited')->nullable()->default(false);
            $table->boolean('processingEdited')->nullable()->default(false);

            $table->unsignedInteger('deletedby')->unsigned()->nullable();
            $table->foreign('deletedby')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rental_payments', function (Blueprint $table) {
            //
        });
    }
}
