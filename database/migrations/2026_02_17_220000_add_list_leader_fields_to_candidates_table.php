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
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('candidate_type')->default('candidate')->after('banner');
            $table->unsignedInteger('list_candidates_count')->nullable()->after('candidate_type');
            $table->string('list_name')->nullable()->after('list_candidates_count');
            $table->string('list_logo')->nullable()->after('list_name');
            $table->boolean('is_actual_list_candidate')->default(true)->after('list_logo');
            $table->foreignId('list_leader_candidate_id')->nullable()->after('is_actual_list_candidate')->constrained('candidates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('list_leader_candidate_id');
            $table->dropColumn([
                'candidate_type',
                'list_candidates_count',
                'list_name',
                'list_logo',
                'is_actual_list_candidate',
            ]);
        });
    }
};
