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
            $table->boolean('is_stopped')->default(false)->after('list_leader_candidate_id');
            $table->foreignId('stopped_by_candidate_id')->nullable()->after('is_stopped')->constrained('candidates')->nullOnDelete();
            $table->timestamp('stopped_at')->nullable()->after('stopped_by_candidate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('stopped_by_candidate_id');
            $table->dropColumn(['is_stopped', 'stopped_at']);
        });
    }
};
