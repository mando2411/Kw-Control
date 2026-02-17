<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contractor_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('requester_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('requester_name');
            $table->string('requester_phone');
            $table->string('status')->default('pending');
            $table->text('decision_note')->nullable();
            $table->timestamp('decision_at')->nullable();
            $table->foreignId('decided_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['candidate_id', 'status']);
            $table->index(['requester_user_id', 'status']);
            $table->unique(['candidate_id', 'requester_user_id'], 'contractor_join_candidate_requester_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_join_requests');
    }
};
