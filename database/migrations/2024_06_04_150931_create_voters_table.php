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
        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('almrgaa')->nullable();
            $table->string('albtn')->nullable();
            $table->string('alfraa')->nullable();
            $table->date('yearOfBirth')->nullable();
            $table->string('btn_almoyhy')->nullable();
            $table->string('tary_kh_alandmam')->nullable();
            $table->string('alrkm_ala_yl_llaanoan')->nullable();
            $table->string('alfkhd')->nullable();
            $table->string('alktaa')->nullable();
            $table->string('alrkm_almd_yn')->nullable();
            $table->string('alsndok')->nullable();
            $table->string('phone1')->nullable();
            $table->string('job')->nullable();
            $table->string('type')->nullable();
            $table->string('region')->nullable();
            $table->string('status')->default(0);
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
        Schema::dropIfExists('voters');
    }
};
