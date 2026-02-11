<?php

namespace App\Imports;

use App\Models\Election;
use App\Models\Voter;
use App\Models\Family;
use App\Models\Selection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class VoterCheck implements ToCollection, WithHeadingRow
{
    private $election;

    public function __construct($election)
    {
        $this->election = Election::findOrFail($election);
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {

            $validRows = $rows->filter(function ($row) {
                return isset($row['mrgaa_aldakhly']);
            });

            $mrgaaAlDakhlyIds = $validRows->pluck('mrgaa_aldakhly')->unique();
            $voters= Voter::whereIn('alrkm_almd_yn',$mrgaaAlDakhlyIds)->where('status',0)->get();
            $voters->each(function($voter){
                $voter->update([
                    'status'=>1,
                    'committee_id'=>1,
                    'attend_id'=>23
                ]);
            });
        });
    }
}
