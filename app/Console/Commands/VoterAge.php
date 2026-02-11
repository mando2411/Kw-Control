<?php

namespace App\Console\Commands;

use App\Models\Voter;
use Illuminate\Console\Command;
use Carbon\Carbon;


class VoterAge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voter:age';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make the Age Of The Voters';

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
            if($voter->alrkm_almd_yn){
                $date=$this->getBirthDateFromId($voter->alrkm_almd_yn);
                $fullName = $voter->name;
                $nameParts = explode(' ', $fullName);
                if (count($nameParts) >= 3) {
                    array_shift($nameParts);
                    $father = implode(' ', $nameParts);
                    array_shift($nameParts);
                    $grand = implode(' ', $nameParts);
                } else {
                    $father = '';
                    $grand = '';
                }
                $voter->update([
                    'yearOfBirth'=>$date->format('Y-m-d'),
                    'age'=>$date->age,
                    'father' => $father ?? null,
                    'grand' => $grand ?? null,
                ]);
                $this->info("Voter Name: {$voter->name}");
            }
            \Log::info("Processing Voter: {$voter->name}");
        });

        $this->info('Voter processing complete.');

        return Command::SUCCESS;
    }

    function getBirthDateFromId($id)
{
    $centuryIndicator = substr($id, 0, 1);

    $century = $centuryIndicator == '2' ? '19' : ($centuryIndicator == '3' ? '20' : null);

    if (!$century) {
        throw new Exception("Invalid ID format. Century indicator must be '2' or '3'.");
    }

    $year = $century . substr($id, 1, 2);
    $month = substr($id, 3, 2);
    $day = substr($id, 5, 2);

    // Create a Carbon date
    try {
        $birthDate = Carbon::createFromDate($year, $month, $day);
    } catch (\Exception $e) {
        throw new Exception("Invalid date extracted from ID.");
    }

    return $birthDate;
}
}
