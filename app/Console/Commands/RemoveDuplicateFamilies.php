<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Family;
use App\Models\Voter;

class RemoveDuplicateFamilies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:duplicate-families';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate families and reassign voters to a single family';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get all families grouped by name and election_id with a count greater than 1
        $families = Family::select('name', 'election_id')
            ->groupBy('name', 'election_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($families as $family) {
            // Get all family records with the same name and election_id
            $duplicateFamilies = Family::where('name', $family->name)
                ->where('election_id', $family->election_id)
                ->get();

            // Keep the first family record, and get its ID
            $mainFamily = $duplicateFamilies->shift();
            $mainFamilyId = $mainFamily->id;

            // Update all voters associated with duplicate families to point to the main family
            Voter::whereIn('family_id', $duplicateFamilies->pluck('id'))
                ->update(['family_id' => $mainFamilyId]);

            // Delete the duplicate family records, except for the main one
            Family::whereIn('id', $duplicateFamilies->pluck('id'))->delete();

            $this->info("Processed family '{$family->name}' with election ID '{$family->election_id}' and removed duplicates.");
        }

        $this->info('All duplicate families have been processed.');

        return 0;
    }
}
