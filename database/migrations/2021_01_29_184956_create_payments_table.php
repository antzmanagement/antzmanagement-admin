<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_contract_id')->unsigned();
            $table->string('uid');
            $table->decimal('price',8,2)->default(0.00);
            $table->decimal('totalpayment',8,2)->default(0.00);
            $table->decimal('penalty',8,2)->default(0.00);
            $table->decimal('processing_fees',8,2)->default(0.00);
            $table->decimal('other_charges',8,2)->default(0.00);
            $table->decimal('outstanding',8,2)->default(0.00);
            $table->date('paymentdate')->nullable();
            $table->longText('remark')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sequence')->default(0);
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
        Schema::dropIfExists('payments');
    }
}
