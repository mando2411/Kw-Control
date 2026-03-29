<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorting_papers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('election_id')->nullable();
            $table->unsignedBigInteger('committee_id');
            $table->unsignedInteger('paper_number');
            $table->unsignedBigInteger('started_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['committee_id', 'paper_number']);
            $table->index('election_id');
            $table->unique(['committee_id', 'paper_number']);
        });

        Schema::create('sorting_paper_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sorting_paper_id');
            $table->unsignedBigInteger('committee_id');
            $table->unsignedBigInteger('candidate_id');
            $table->integer('delta_votes');
            $table->string('action_type', 20);
            $table->integer('action_value')->default(0);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['sorting_paper_id', 'candidate_id']);
            $table->index(['committee_id', 'candidate_id']);
            $table->index('created_by_user_id');

            $table->foreign('sorting_paper_id')->references('id')->on('sorting_papers')->cascadeOnDelete();
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorting_paper_events');
        Schema::dropIfExists('sorting_papers');
    }
};
