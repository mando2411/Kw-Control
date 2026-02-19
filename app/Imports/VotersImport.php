<?php

namespace App\Imports;

use App\Helpers\ArabicHelper;
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
            foreach ($rows as $i=>$row) {
                    $rowData = $this->mapRow($row->toArray());

                    if (empty($rowData['alasm'])) {
                        $this->skippedCount++;
                        continue;
                    }

                    try {
                        [$voter, $state] = $this->processVoter($rowData);
                        $this->processSelection($rowData);

                        if (!$voter->election()->where('election_id', $this->election->id)->exists()) {
                            $voter->election()->attach($this->election->id);
                        }
                        if ($state === 'created') {
                            $this->createdCount++;
                        } elseif ($state === 'updated') {
                            $this->existingCount++;
                            $this->updatedCount++;
                        } else {
                            $this->existingCount++;
                            $this->duplicateSkippedCount++;
                        }
                        $this->successCount++;
                    } catch (\Throwable $exception) {
                        $this->failedCount++;
                        Log::warning('Voters import row failed', [
                            'row_number' => $i + 2,
                            'election_id' => $this->election->id,
                            'name' => $rowData['alasm'] ?? null,
                            'civil_id' => $rowData['alrkm_almdny'] ?? null,
                            'error' => $exception->getMessage(),
                        ]);
                    }
            }
        });
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

    private function processVoter(array $row): array
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
        $normalizedCivilId = $this->normalizeIdentifier($row['alrkm_almdny'] ?? null);

        if (!empty($normalizedCivilId)) {
            try {
                $birthDate = $this->getBirthDateFromId($normalizedCivilId);
                $dateOfBirth = $birthDate->format('Y-m-d');
                $age = $birthDate->age;
            } catch (\Exception $e) {
                // Ignore invalid birth dates to avoid slowing down imports with excessive logging.
            }
        }

        $nameParts = explode(' ', $row['alasm']);
        $father = $nameParts[1] ?? '';
        $grand = $nameParts[2] ?? '';

        $voterData = [
            'name'                         => $row['alasm'],
            'normalized_name'              => ArabicHelper::normalizeArabic((string) ($row['alasm'] ?? '')),
            'almrgaa'                      => $row['mrgaa'] ?? null,
            'albtn'                        => $row['albtn'] ?? null,
            'alfraa'                       => $row['alfraa'] ?? null,
            'btn_almoyhy'                  => $row['btn_almoyhy'] ?? null,
            'tary_kh_alandmam'             => $row['tarykh_alandmam'] ?? null,
            'alrkm_ala_yl_llaanoan'        => $row['alrkm_ala_yl_llaanoan'] ?? null,
            'alktaa'                       => $row['alktaah'] ?? null,
            'alrkm_almd_yn'                => $normalizedCivilId,
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

        if (!empty($normalizedCivilId)) {
            $voter = Voter::where('alrkm_almd_yn', $normalizedCivilId)->first();
            if ($voter) {
                $voter->fill($voterData);
                if ($voter->isDirty()) {
                    $voter->save();
                    return [$voter, 'updated'];
                }

                return [$voter, 'unchanged'];
            }
        }

        $voter = Voter::create($voterData);

        return [$voter, 'created'];
    }

    private function mapRow(array $row): array
    {
        return [
            'alasm' => $this->value($row, 'alasm', ['name', 'full_name', 'fullname', 'الاسم', 'اسم']),
            'alaaaylh' => $this->value($row, 'alaaaylh', ['family', 'family_name', 'العائلة', 'اسم_العائلة']),
            'alrkm_almdny' => $this->value($row, 'alrkm_almdny', ['civil_id', 'civilid', 'id', 'national_id', 'الرقم_المدني']),
            'mrgaa' => $this->value($row, 'mrgaa', ['almrgaa', 'reference', 'المرجع']),
            'albtn' => $this->value($row, 'albtn', ['btn', 'البطن']),
            'alfraa' => $this->value($row, 'alfraa', ['elfar3', 'far3', 'الفرع']),
            'btn_almoyhy' => $this->value($row, 'btn_almoyhy', ['btn_almouhy', 'موضحي']),
            'tarykh_alandmam' => $this->value($row, 'tarykh_alandmam', ['tary_kh_alandmam', 'join_date', 'تاريخ_الانضمام']),
            'alrkm_ala_yl_llaanoan' => $this->value($row, 'alrkm_ala_yl_llaanoan', ['address_number', 'رقم_العنوان']),
            'alktaah' => $this->value($row, 'alktaah', ['alktaa', 'القطعة']),
            'alkyd_ao_alsndok' => $this->value($row, 'alkyd_ao_alsndok', ['alsndok', 'box', 'الصندوق']),
            'alfkhd' => $this->value($row, 'alfkhd', ['elfa5z', 'الفخذ']),
            'alnoaa' => $this->value($row, 'alnoaa', ['type', 'gender', 'النوع', 'الجنس']),
            'alhatf1' => $this->value($row, 'alhatf1', ['phone', 'phone1', 'الهاتف', 'الهاتف1']),
            'almntk' => $this->value($row, 'almntk', ['region', 'المنطقة']),
            'alhatf2' => $this->value($row, 'alhatf2', ['phone2', 'الهاتف2']),
            'cod1' => $this->value($row, 'cod1', ['code1']),
            'cod2' => $this->value($row, 'cod2', ['code2']),
            'cod3' => $this->value($row, 'cod3', ['code3']),
            'hal_alkyd' => $this->value($row, 'hal_alkyd', ['restricted', 'register_status', 'حالة_القيد']),
            'street' => $this->value($row, 'street', ['الشارع']),
            'home' => $this->value($row, 'home', ['house', 'المنزل']),
            'elharaa' => $this->value($row, 'elharaa', ['alharaa', 'الجادة']),
        ];
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

        $candidateTokens = collect($candidates)
            ->map(fn($candidate) => $this->normalizeHeader((string) $candidate))
            ->filter()
            ->values()
            ->all();

        if ($field === 'alaaaylh') {
            $candidateTokens = array_values(array_unique(array_merge($candidateTokens, [
                'family',
                'familyname',
                'aaylh',
                'aaaylh',
                'عائله',
                'العائله',
                'العايله',
            ])));
        }

        foreach ($this->normalizedHeaderToOriginal as $normalizedHeader => $originalHeader) {
            foreach ($candidateTokens as $token) {
                if ($token === '') {
                    continue;
                }

                if (str_contains($normalizedHeader, $token) || str_contains($token, $normalizedHeader)) {
                    $this->resolvedColumns[$field] = $originalHeader;
                    return $this->resolvedColumns[$field];
                }
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
        $header = preg_replace('/^\xEF\xBB\xBF/u', '', $header);
        $normalized = ArabicHelper::normalizeArabic($header);
        $normalized = mb_strtolower($normalized, 'UTF-8');
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', '', $normalized);

        return trim((string) $normalized);
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

    private function normalizeIdentifier(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = is_string($value) ? trim($value) : (string) $value;
        if ($raw === '') {
            return null;
        }

        if (is_numeric($raw)) {
            $raw = (string) (str_contains($raw, 'E') || str_contains($raw, 'e')
                ? number_format((float) $raw, 0, '.', '')
                : preg_replace('/\.0+$/', '', $raw));
        }

        $normalized = preg_replace('/\D+/', '', $raw);
        return $normalized !== '' ? $normalized : null;
    }
}
