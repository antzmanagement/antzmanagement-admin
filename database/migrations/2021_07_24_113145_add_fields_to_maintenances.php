<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToMaintenances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->unsignedInteger('room_check_id')->unsigned()->nullable();
            $table->unsignedInteger('tenant_id')->unsigned()->nullable();
            $table->unsignedInteger('issue_by')->unsigned()->nullable();
            $table->boolean('claim_by_tenant')->default(false);
            $table->boolean('paid')->default(false);
            $table->date('maintenance_date')->nullable();

            $table->string('receiptno')->nullable();
            $table->string('paymentmethod')->nullable();
            $table->string('receive_from')->nullable();
            $table->unsignedInteger('issueby')->unsigned()->nullable();
            $table->decimal('processing_fees',8,2)->default(0.00);
            $table->decimal('payment',8,2)->default(0.00);
            $table->decimal('outstanding',8,2)->default(0.00);
            $table->date('paymentdate')->nullable();


            $table->foreign('room_check_id')
            ->references('id')
            ->on('room_checks')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->foreign('tenant_id')
            ->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('restrict');

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
        Schema::table('maintenances', function (Blueprint $table) {
            //
        });
    }
}
