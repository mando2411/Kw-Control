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
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class VoterCheck implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    private $election;
    private int $totalRows = 0;
    private int $successCount = 0;
    private int $skippedCount = 0;
    private int $failedCount = 0;
    private int $createdCount = 0;
    private int $existingCount = 0;
    private int $updatedCount = 0;
    private int $duplicateSkippedCount = 0;

    public function __construct($election)
    {
        $this->election = Election::findOrFail($election);
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $this->totalRows = $rows->count();
        DB::transaction(function () use ($rows) {
            try {
                $validRows = $rows->filter(function ($row) {
                    return isset($row['mrgaa_aldakhly']);
                });

                $this->skippedCount += $rows->count() - $validRows->count();

                $mrgaaAlDakhlyIds = $validRows->pluck('mrgaa_aldakhly')->unique();
                $voters = Voter::whereIn('alrkm_almd_yn', $mrgaaAlDakhlyIds)
                    ->where('status', 0)
                    ->get();

                $this->successCount += $voters->count();
                $this->updatedCount += $voters->count();
                $this->existingCount += $voters->count();
                $unmatched = $mrgaaAlDakhlyIds->count() - $voters->count();
                if ($unmatched > 0) {
                    $this->skippedCount += $unmatched;
                }

                $voters->each(function($voter){
                    $voter->update([
                        'status'=>1,
                        'committee_id'=>1,
                        'attend_id'=>23
                    ]);
                });
            } catch (\Throwable $exception) {
                $this->failedCount++;
                Log::error('VoterCheck import failed:', [
                    'error' => $exception->getMessage(),
                ]);
            }
        });
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function getExistingCount(): int
    {
        return $this->existingCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getDuplicateSkippedCount(): int
    {
        return $this->duplicateSkippedCount;
    }
}
