<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->foreignId('election_id')->nullable()->after('type')->constrained('elections')->nullOnDelete();
        });

        // Backfill when a school is linked to committees from exactly one election.
        $schoolIds = DB::table('schools')->pluck('id');

        foreach ($schoolIds as $schoolId) {
            $electionIds = DB::table('committees')
                ->where('school_id', $schoolId)
                ->whereNotNull('election_id')
                ->distinct()
                ->pluck('election_id')
                ->values();

            if ($electionIds->count() === 1) {
                DB::table('schools')
                    ->where('id', $schoolId)
                    ->update(['election_id' => (int) $electionIds->first()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropConstrainedForeignId('election_id');
        });
    }
};
