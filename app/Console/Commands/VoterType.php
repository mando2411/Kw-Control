<?php

namespace App\Console\Commands;

use App\Enums\Type;
use App\Models\Voter;
use Illuminate\Console\Command;
use Carbon\Carbon;

class VoterType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voter:type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make the Type Of The Voters';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Command started...');

        Voter::chunk(1000, function ($voters) {
            foreach ($voters as $voter) {
                if ($voter->type != 'ذكر') {
                    $voter->update(['type' => Type::FEMALE->value]);
                } else {
                    $voter->update(['type' => Type::MALE->value]);
                }
                $this->info("Processed Voter: {$voter->name}");
                \Log::info("Processing Voter: {$voter->name}");
            }
        });

        $this->info('Command completed.');
        return Command::SUCCESS;
    }
}
