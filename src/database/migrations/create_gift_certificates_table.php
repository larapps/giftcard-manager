<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_certificates', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('store_id')->unsigned()->index()->nullable();
            $table->bigInteger('file_id')->unsigned()->index()->nullable();
            $table->bigInteger('bc_id')->unsigned()->index()->nullable();

            $table->string("to_name");
            $table->string("to_email");
            $table->string("from_name");
            $table->string("from_email");
            $table->integer("amount");
            $table->integer("balance")->nullable();
            $table->string("purchase_date")->nullable();
            $table->string("expiry_date")->nullable();
            $table->integer("customer_id")->nullable();
            $table->string("template")->nullable();
            $table->string("message")->nullable();
            $table->string("code")->nullable();
            $table->string("status")->nullable();
            $table->string("currency_code")->nullable();
            $table->string("table_status")->nullable();
            $table->string("table_output")->nullable();
            $table->text("table_output_reason")->nullable();
            $table->string("type")->nullable();
            $table->bigInteger("order_id")->nullable();

            $table->foreign('file_id')->references('id')->on('file_status')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('bc_stores')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bc_stores');
    }
};
