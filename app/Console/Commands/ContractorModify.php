<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contractor;
use App\Models\Voter;

class ContractorModify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'con:mod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modify the voters relationship in the Contractor model';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $contractors = Contractor::all();

        $contractors->each(function ($contractor) {
            $election_id = $contractor->creator->election_id;

            $contractor->voters->each(function ($voter) use ($election_id, $contractor) {
                // Check if the voter is associated with the given election_id
                $hasMatchingElection = $voter->election()->where('id', $election_id)->exists();

                if ($hasMatchingElection) {
                    // Do nothing if the voter is already linked to the election
                    return;
                }

                // Find a replacement voter with the same 'alrkm_almd_yn' and matching election_id
                $replacementVoter = Voter::where('alrkm_almd_yn', $voter->alrkm_almd_yn)
                    ->whereHas('election', function ($query) use ($election_id) {
                        $query->where('id', $election_id);
                    })
                    ->first();

                if ($replacementVoter) {
                    // Replace the current voter in the pivot table with the new voter
                    $contractor->voters()->detach($voter->id);
                    $contractor->voters()->attach($replacementVoter->id, [
                        'percentage' => $voter->pivot->percentage, // Preserve percentage from pivot table
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
            $this->info('Contractor '.$contractor->name.' modified successfully.');

        });

        $this->info('Contractor voter relationships modified successfully.');
        return Command::SUCCESS;
    }
}
