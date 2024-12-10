<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('file_status', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id')->unsigned()->index()->nullable();
            $table->string("file_name");
            $table->string("status");
            $table->string("type")->nullable();
            $table->text("additional_info")->nullable();
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('bc_stores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_status');
    }
};
