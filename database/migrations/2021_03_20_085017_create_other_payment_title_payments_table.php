<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherPaymentTitlePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_payment_titles_payments', function (Blueprint $table) {
            $table->unsignedInteger('payment_id')->unsigned();
            $table->unsignedInteger('other_payment_title_id')->unsigned();
            $table->decimal('price',8,2)->default(0.00);
            $table->longText('remark')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('payment_id')
            ->references('id')
            ->on('payments')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('other_payment_title_id')
            ->references('id')
            ->on('other_payment_titles')
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
        Schema::dropIfExists('other_payment_title_payments');
    }
}
