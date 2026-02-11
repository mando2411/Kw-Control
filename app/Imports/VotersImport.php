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

class VotersImport implements ToCollection, WithHeadingRow
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
            foreach ($rows as $i=>$row) {
                    if (empty($row['alasm'])) {
                        Log::warning('Missing data in row:', $row->toArray());
                        continue;
                    }
    
                    $voter = $this->processVoter($row->toArray());
                    $this->processSelection($row->toArray());
    
                    if (!$voter->election()->where('election_id', $this->election->id)->exists()) {
                        $voter->election()->attach($this->election->id);
                    }
            }
        });
    }

    private function processVoter(array $row)
    {

        $familyId = null;
        if (!empty($row['alaaaylh'])) {
            $family = Family::firstOrCreate([
                'name' => $row['alaaaylh'],
                'election_id'=>$this->election->id
        ]);
            $familyId = $family->id;
        }

        $dateOfBirth = null;
        $age = null;
        if (!empty($row['alrkm_almdny'])) {
            try {
                $birthDate = $this->getBirthDateFromId($row['alrkm_almdny']);
                $dateOfBirth = $birthDate->format('Y-m-d');
                $age = $birthDate->age;
            } catch (\Exception $e) {
                Log::warning('Invalid birth date:', ['id' => $row['alrkm_almdny']]);
            }
        }

        $nameParts = explode(' ', $row['alasm']);
        $father = $nameParts[1] ?? '';
        $grand = $nameParts[2] ?? '';

        $voterData = [
            'name'                         => $row['alasm'],
            'almrgaa'                      => $row['mrgaa'] ?? null,
            'albtn'                        => $row['albtn'] ?? null,
            'alfraa'                       => $row['alfraa'] ?? null,
            'btn_almoyhy'                  => $row['btn_almoyhy'] ?? null,
            'tary_kh_alandmam'             => $row['tarykh_alandmam'] ?? null,
            'alrkm_ala_yl_llaanoan'        => $row['alrkm_ala_yl_llaanoan'] ?? null,
            'alktaa'                       => $row['alktaah'] ?? null,
            'alrkm_almd_yn'                => $row['alrkm_almdny'] ?? null,
            'alsndok'                      => $row['alkyd_ao_alsndok'] ?? null,
            'alfkhd'                       => $row['alfkhd'] ?? null,
            'type'                         => $row['alnoaa'] ?? null,
            'yearOfBirth'                  => $dateOfBirth,
            'age'                          => $age,
            'phone1'                       => $row['alhatf1'] ?? null,
            'region'                       => $row['almntk'] ?? null,
            'phone2'                       => $row['alhatf2'] ?? null,
            'cod1'                         => $row['cod1'] ?? null,
            'cod2'                         => $row['cod2'] ?? null,
            'cod3'                         => $row['cod3'] ?? null,
            'father'                       => $father,
            'grand'                        => $grand,
            'restricted'                   => $row['hal_alkyd'] ?? 'غير مقيد',
            'family_id'                    => $familyId,
        ];

        $voter = Voter::firstOrCreate(
            $voterData// Data to create if record doesn't exist
        );

        return $voter;
    }

    private function processSelection(array $row)
    {
        Selection::firstOrCreate([
            'cod1'    => $row['cod1'] ?? null,
            'cod2'    => $row['cod2'] ?? null,
            'cod3'    => $row['cod3'] ?? null,
            'street'  => $row['street'] ?? null,
            'home'    => $row['home'] ?? null,
            'alharaa' => $row['elharaa'] ?? null,
            'albtn'   => $row['albtn'] ?? null,
            'alfraa'  => $row['alfraa'] ?? null,
            'alktaa'  => $row['alktaah'] ?? null,
            'alfkhd'  => $row['alfkhd'] ?? null,
        ]);
    }

    private function getBirthDateFromId($id)
    {
        $centuryIndicator = substr($id, 0, 1);
        $century = $centuryIndicator == '2' ? '19' : ($centuryIndicator == '3' ? '20' : null);

        if (!$century) {
            throw new \Exception("Invalid ID format. Century indicator must be '2' or '3'.");
        }

        $year = $century . substr($id, 1, 2);
        $month = substr($id, 3, 2);
        $day = substr($id, 5, 2);

        // Create a Carbon date
        return Carbon::createFromDate($year, $month, $day);
    }
}
