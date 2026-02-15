<?php

namespace App\Console\Commands;

use App\Enums\Type;
use App\Models\Voter;
use App\Models\Family;
use Illuminate\Console\Command;
use Carbon\Carbon;


class VoterModify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voter:modify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make the Type Of The Voters';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $voters = Voter::all();

        $voters->each(function($voter) {

            $nameParts = explode(' ', $voter->name);
            $father = implode(' ', array_slice($nameParts, 1));
            $grand = implode(' ', array_slice($nameParts, 2));
            $voter->update([
                'father' => $father ?? null,
                'grand' => $grand ?? null,
                ]);
            $this->info("Voter Name: {$voter->name}");
            \Log::info("Processing Voter: {$voter->name}");
        });

        $this->info('Voter processing complete.');

        return Command::SUCCESS;
    }

        public function family(Voter $voter)
        {

            $family = $voter->family;

            $voterElectionIds = $voter->election()->pluck('id')->toArray();

            $matchingFamily = Family::where('name', $family->name)
                ->whereIn('election_id', $voterElectionIds)
                ->first();
            if($matchingFamily && $matchingFamily->id != $voter->family_id  ){
                $voter->update([
                    'family_id'=>$matchingFamily->id,
                ]);
            }else{
                $newFamily = Family::create([
                    'name' => $family->name,
                    'election_id' => $voterElectionIds[0], // Use the first election_id from the voter's elections
                ]);

                // Update the voter's family to the newly created family
                $voter->update([
                    'family_id' => $newFamily->id,
                ]);

            }
    }


}
