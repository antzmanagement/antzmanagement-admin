<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_services', function (Blueprint $table) {
            
            $table->increments('id');
            $table->unsignedInteger('payment_id')->unsigned();
            $table->unsignedInteger('service_id')->unsigned();
            $table->decimal('price',8,2)->default(0.00);
            $table->longText('remark')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('payment_id')
            ->references('id')
            ->on('payments')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('service_id')
            ->references('id')
            ->on('services')
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
        Schema::dropIfExists('payments_services');
    }
}
