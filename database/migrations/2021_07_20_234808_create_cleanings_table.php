<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCleaningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cleanings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_id')->unsigned()->nullable();
            $table->unsignedInteger('room_check_id')->unsigned()->nullable();
            $table->unsignedInteger('owner_id')->unsigned()->nullable();
            $table->unsignedInteger('tenant_id')->unsigned()->nullable();
            $table->unsignedInteger('issue_by')->unsigned()->nullable();
            $table->string('uid');
            $table->string('cleaning_type')->nullable();
            $table->string('cleaning_status')->nullable();
            $table->longText('remark')->nullable();
            $table->decimal('price',8,2)->default(0.00);
            $table->date('cleaning_date')->nullable();
            $table->boolean('claim_by_owner')->default(false);
            $table->boolean('claim_by_tenant')->default(false);
            $table->boolean('status')->default(true);

            $table->boolean('paid')->default(false);
            $table->string('receiptno')->nullable();
            $table->string('paymentmethod')->nullable();
            $table->string('receive_from')->nullable();
            $table->decimal('processing_fees',8,2)->default(0.00);
            $table->decimal('payment',8,2)->default(0.00);
            $table->decimal('outstanding',8,2)->default(0.00);
            $table->date('paymentdate')->nullable();
            $table->timestamps();

            $table->foreign('room_id')
            ->references('id')
            ->on('rooms')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('room_check_id')
            ->references('id')
            ->on('room_checks')
            ->onUpdate('cascade')
            ->onDelete('restrict');


            $table->foreign('owner_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('tenant_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('issue_by')
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
        Schema::dropIfExists('cleanings');
    }
}
