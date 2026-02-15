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
        Schema::table('voters', function (Blueprint $table) {
            $table->foreignId('committee_id')->nullable()->constrained('committees')->nullOnDelete();
        });
        Schema::table('representatives', function (Blueprint $table) {
            $table->foreignId('committee_id')->nullable()->constrained('committees')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voters', function (Blueprint $table) {
            //
        });
        Schema::table('representatives', function (Blueprint $table) {
            //
        });
    }
};
