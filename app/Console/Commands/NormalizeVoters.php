<?php
namespace App\Console\Commands;

use App\Models\Voter;
use App\Helpers\ArabicHelper;
use Illuminate\Console\Command;

class NormalizeVoters extends Command
{
protected $signature = 'voters:normalize';
protected $description = 'Normalize Arabic names in the voters table';

public function handle()
{
Voter::chunk(1000, function ($voters) {
    foreach ($voters as $voter) {
        $voter->normalized_name = ArabicHelper::normalizeArabic($voter->name);
        $voter->soundex_name = ArabicHelper::arabicSoundex($voter->name);
        $voter->save();
        $this->info('voter'.' '.$voter->id.' '.'normalized');
    }

});

$this->info('Voters normalized successfully.');
}
}
