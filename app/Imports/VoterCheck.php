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
    private array $resolvedColumns = [];
    private array $normalizedHeaderToOriginal = [];
    private bool $headersBootstrapped = false;
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
                $validRows = $rows->map(function ($row) {
                        return $row->toArray();
                    })
                    ->filter(function (array $row) {
                        return !empty($this->value($row, 'mrgaa_aldakhly', ['alrkm_almdny', 'civil_id', 'civilid', 'id', 'الرقم_المدني']));
                });

                $this->skippedCount += $rows->count() - $validRows->count();

                $mrgaaAlDakhlyIds = $validRows
                    ->map(function (array $row) {
                        return $this->value($row, 'mrgaa_aldakhly', ['alrkm_almdny', 'civil_id', 'civilid', 'id', 'الرقم_المدني']);
                    })
                    ->filter()
                    ->unique();
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
            }
        });
    }

    private function value(array $row, string $field, array $aliases = []): mixed
    {
        $columnKey = $this->resolveColumnKey($row, $field, $aliases);
        if (!$columnKey || !array_key_exists($columnKey, $row)) {
            return null;
        }

        $value = $row[$columnKey];
        if (is_string($value)) {
            $value = trim($value);
            return $value === '' ? null : $value;
        }

        return $value;
    }

    private function resolveColumnKey(array $row, string $field, array $aliases = []): ?string
    {
        $this->bootstrapHeaders($row);

        if (array_key_exists($field, $this->resolvedColumns)) {
            return $this->resolvedColumns[$field];
        }

        $candidates = array_merge([$field], $aliases);

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeHeader($candidate);
            if ($normalized !== '' && isset($this->normalizedHeaderToOriginal[$normalized])) {
                $this->resolvedColumns[$field] = $this->normalizedHeaderToOriginal[$normalized];
                return $this->resolvedColumns[$field];
            }
        }

        $this->resolvedColumns[$field] = null;
        return null;
    }

    private function bootstrapHeaders(array $row): void
    {
        if ($this->headersBootstrapped) {
            return;
        }

        foreach (array_keys($row) as $header) {
            $normalized = $this->normalizeHeader((string) $header);
            if ($normalized !== '' && !isset($this->normalizedHeaderToOriginal[$normalized])) {
                $this->normalizedHeaderToOriginal[$normalized] = (string) $header;
            }
        }

        $this->headersBootstrapped = true;
    }

    private function normalizeHeader(string $header): string
    {
        $normalized = mb_strtolower($header, 'UTF-8');
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', '', $normalized);

        return trim((string) $normalized);
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
