<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_contract_id')->unsigned();
            $table->string('uid');
            $table->decimal('price',8,2)->default(0.00);
            $table->decimal('payment',8,2)->default(0.00);
            $table->decimal('penalty',8,2)->default(0.00);
            $table->decimal('processing_fees',8,2)->default(0.00);
            $table->decimal('service_fees',8,2)->default(0.00);
            $table->decimal('outstanding',8,2)->default(0.00);
            $table->boolean('paid')->default(false);
            $table->date('paymentdate')->nullable();
            $table->date('rentaldate');
            $table->longText('remark')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('room_contract_id')
            ->references('id')
            ->on('room_contracts')
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
        Schema::dropIfExists('rental_payments');
    }
}
