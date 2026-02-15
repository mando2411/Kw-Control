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
        Schema::create('selections', function (Blueprint $table) {
            $table->id();
            $table->string('cod1')->nullable();
            $table->string('cod2')->nullable();
            $table->string('cod3')->nullable();
            $table->string('alfkhd')->nullable();
            $table->string('alktaa')->nullable();
            $table->string('albtn')->nullable();
            $table->string('alfraa')->nullable();
            $table->string('street')->nullable();
            $table->string('home')->nullable();
            $table->string('alharaa')->nullable();
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
        Schema::dropIfExists('selections');
    }
};
