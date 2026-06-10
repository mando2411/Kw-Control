<?php

namespace App\Imports;

use App\Helpers\ArabicHelper;
use App\Models\Voter;
use App\Models\Contractor;
use App\Models\ContractorVoter;
use App\Models\Election;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContractorVotersImport implements ToCollection, WithHeadingRow
{
    private $contractor_id;
    private $sheet_contractor_id;
    private $success_count;
    private $failed_count;
    private $repeat_count;
    private $missing_id_count;
    private $voter_not_found_count;
    private $not_allowed_count;
    private $contractor_not_found_count;
    private $msg;
    private array $failedRows = [];
    private array $resolvedColumns = [];
    private array $normalizedHeaderToOriginal = [];
    private bool $headersBootstrapped = false;
    //=================================================================================================
    public function __construct($contractor_id){
        $this->contractor_id        = $contractor_id;
        $this->sheet_contractor_id  = 0;
        $this->success_count        = 0;
        $this->failed_count         = 0;
        $this->repeat_count         = 0;
        $this->missing_id_count     = 0;
        $this->voter_not_found_count= 0;
        $this->not_allowed_count    = 0;
        $this->contractor_not_found_count = 0;
        $this->msg                  = '';
        $this->failedRows          = [];
    }
    //=================================================================================================
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows){
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                try {
                    $row_data = $row->toArray();
                    $has_data = count(array_filter($row_data, function ($value) {
                        return $value !== null && $value !== '';
                    })) > 0;

                    if (!$has_data) {
                        continue;
                    }

                    Log::info('----------------------------');
                    Log::info($row);
                    Log::info('----------------------------');
                    //=============================================================================================================
                    $contractorName = $this->value($row_data, 'asm_almtaahd', [
                        'asm_almtahed',
                        'asm_almtaahd',
                        'اسم_المتعهد_الفرعي',
                        'اسم_المتعهد',
                        'اسم_المتعاقد',
                        'اسم_المتعهد الفرعي',
                        'sub_contractor',
                        'subcontractor'
                    ]);
                    $voterName = $this->value($row_data, 'alasm', [
                        'alasm',
                        'name',
                        'full_name',
                        'fullname',
                        'الاسم',
                        'اسم'
                    ]);
                    $nationalId = $this->normalizeIdentifier($this->value($row_data, 'alrkm_almdn', [
                        'alrkm_almdn',
                        'alrkm_almdny',
                        'alrkm_almd_yn',
                        'alsndok',
                        'registration_number',
                        'civil_id',
                        'civilid',
                        'id',
                        'national_id',
                        'الرقم_المدني',
                        'الرقم_المدنى',
                        'رقم_القيد'
                    ]));

                    if (($this->contractor_id) == 0) {
                    if ($contractorName) {
                        $constractor_detail = Contractor::withoutGlobalScopes()
                            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($contractorName), 'UTF-8')])
                            ->first();
                        if (!$constractor_detail) {
                            $constractor_detail = Contractor::withoutGlobalScopes()
                                ->where('name', 'like', '%' . trim($contractorName) . '%')
                                ->first();
                        }
                        if ($constractor_detail) {
                            $this->sheet_contractor_id = $constractor_detail->id;
                            Log::info('----------------------------');
                            Log::info('contractor_id : '.$this->sheet_contractor_id);
                            Log::info('contractor_name : '.$constractor_detail->name);
                            Log::info('----------------------------');
                        } else {
                            $this->failed_count++;
                            $this->contractor_not_found_count++;
                            $this->msg = 'لم يتم العثور على المتعهد الفرعى في الشيت.';
                            Log::warning('Contractor name not found in sheet row', ['contractor_name' => $contractorName, 'row' => $row_data]);
                            continue;
                        }
                    } else {
                        if ($nationalId === null) {
                            continue;
                        }
                        $this->msg = 'تاكد من ادخال قيمه للمتعهد الفرعى فى النموذج ';
                        $this->failed_count++;
                        continue;
                    }
                }
                //=============================================================================================================
                if ($nationalId) {
                    Log::info($nationalId);
                    $voter_detail = Voter::withoutGlobalScopes()->where('alrkm_almd_yn', $nationalId);
                    if ($voter_detail->count() == 0) {
                        Log::info('Trying fallback search using contains and name', ['identifier' => $nationalId, 'name' => $voterName]);
                        $fallbackQuery = Voter::withoutGlobalScopes()
                            ->where('alrkm_almd_yn', 'like', '%' . $nationalId . '%');

                        if ($voterName) {
                            $normalizedQueryName = $this->normalizeText(ArabicHelper::normalizeArabic($voterName));
                            $normalizedQueryName = str_replace(['ى', 'ي'], 'ي', preg_replace('/\s+/u', '', $normalizedQueryName));
                            $fallbackQuery->where(function ($query) use ($normalizedQueryName, $voterName) {
                                $query->whereRaw("REPLACE(REPLACE(REPLACE(TRIM(normalized_name), ' ', ''), 'ى', 'ي'), 'ي', 'ي') LIKE ?", ['%' . $normalizedQueryName . '%'])
                                      ->orWhere('name', 'like', '%' . $voterName . '%');
                            });
                        }

                        $fallbackCount = $fallbackQuery->count();
                        Log::info('Fallback search count', ['count' => $fallbackCount, 'identifier' => $nationalId, 'name' => $voterName]);

                        if ($fallbackCount > 0) {
                            Log::info('Fallback voter search found candidates', ['identifier' => $nationalId, 'name' => $voterName]);
                            $voter_detail = $fallbackQuery;
                        } else {
                            $numericIdentifier = ctype_digit($nationalId);
                            if ($numericIdentifier && mb_strlen($nationalId) <= 6) {
                                $logKey = 'alsndok';
                                $alsndokQuery = Voter::withoutGlobalScopes()->where('alsndok', $nationalId);
                                if ($voterName) {
                                    $normalizedQueryName = $this->normalizeText(ArabicHelper::normalizeArabic($voterName));
                                    $normalizedQueryName = str_replace(['ى', 'ي'], 'ي', preg_replace('/\s+/u', '', $normalizedQueryName));
                                    $alsndokQuery->where(function ($query) use ($normalizedQueryName, $voterName) {
                                        $query->whereRaw("REPLACE(REPLACE(REPLACE(TRIM(normalized_name), ' ', ''), 'ى', 'ي'), 'ي', 'ي') LIKE ?", ['%' . $normalizedQueryName . '%'])
                                              ->orWhere('name', 'like', '%' . $voterName . '%');
                                    });
                                }
                                $alsndokCount = $alsndokQuery->count();
                                Log::info('Registration number fallback search count', ['count' => $alsndokCount, 'alsndok' => $nationalId, 'name' => $voterName]);
                                if ($alsndokCount > 0) {
                                    Log::info('Registration number fallback found candidates', ['alsndok' => $nationalId, 'name' => $voterName]);
                                    $voter_detail = $alsndokQuery;
                                }
                            }
                        }
                    }

                    if ($voter_detail->count() == 0) {
                        $this->failed_count++;
                        $this->voter_not_found_count++;
                        $this->recordFailedRow($row_data, 'voter_not_found', ['identifier' => $nationalId]);
                        Log::warning('Voter not found by national id', ['national_id' => $nationalId, 'row' => $row_data]);
                    } elseif ($voter_detail->count() == 1) {
                        $voter = $voter_detail->first();
                        Log::info($voter);
                        if ($voter) {
                            $this->handleAddLogic($voter, false, $row_data);
                        } else {
                            Log::info('no voter');
                            $this->failed_count++;
                        }
                    } else {
                        $rowResolved = false;
                        foreach ($voter_detail->get() as $voter) {
                            $status = $this->handleAddLogic($voter, true, $row_data);
                            if ($status === 'success' || $status === 'repeat' || $status === 'failed') {
                                $rowResolved = true;
                                if ($status === 'success') {
                                    break;
                                }
                            }
                        }

                        if (! $rowResolved) {
                            $this->failed_count++;
                            $this->not_allowed_count++;
                            $this->recordFailedRow($row_data, 'not_allowed', ['identifier' => $nationalId]);
                        }
                    }
                } else {
                    Log::warning('Missing data in row:', $row_data);
                    $this->failed_count++;
                    $this->missing_id_count++;
                    $this->recordFailedRow($row_data, 'missing_identifier');
                    $this->msg = 'تاكد من ادخال قيمه للرقم المدنى ';
                    continue;
                }
                //=============================================================================================================
                Log::info('----------------------------');
            } catch (\Throwable $e) {
                $this->failed_count++;
                $this->msg = 'حدث خطأ أثناء استيراد الصف.';
                Log::error('ContractorVotersImport row failed', [
                    'row' => $row_data ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                continue;
            }
            }
        });
        Log::info('----------------------------');
        Log::info('success_count : '.$this->success_count);
        Log::info('failed_count : '.$this->failed_count);
        Log::info('repeat_count : '.$this->repeat_count);
        Log::info('----------------------------');
        // return ['success_count'=>$this->success_count,'failed_count'=>$this->failed_count];
    }
    //=================================================================================================
    public function getSuccessCount(){
        return $this->success_count;
    }
    //=================================================================================================
    public function getFailedCount(){
        return $this->failed_count;
    }
    //=================================================================================================
    public function getRepeatedCount(){
        return $this->repeat_count;
    }
    //=================================================================================================
    public function getMsg(){
        return $this->msg;
    }

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }

    public function getTargetContractorId(){
        return ($this->contractor_id == 0) ? $this->sheet_contractor_id : $this->contractor_id;
    }

    public function getSheetContractorId(){
        return $this->sheet_contractor_id;
    }

    public function getNotAllowedCount(){
        return $this->not_allowed_count;
    }

    public function getVoterNotFoundCount(){
        return $this->voter_not_found_count;
    }

    public function getContractorNotFoundCount(){
        return $this->contractor_not_found_count;
    }
    //=================================================================================================
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
        $normalized = mb_strtolower($header, 'UTF-8');
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', '', $normalized);

        return trim((string) $normalized);
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

        $raw = $this->convertArabicDigits($raw);

        if (is_numeric($raw)) {
            $raw = (string) (str_contains($raw, 'E') || str_contains($raw, 'e')
                ? number_format((float) $raw, 0, '.', '')
                : preg_replace('/\.0+$/', '', $raw));
        }

        $normalized = preg_replace('/\D+/', '', $raw);
        return $normalized !== '' ? $normalized : null;
    }

    private function convertArabicDigits(string $value): string
    {
        $arabicDigits = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $westernDigits = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($arabicDigits, $westernDigits, $value);
    }

    private function normalizeText(string $value): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($value)), 'UTF-8');
    }
    //=================================================================================================
    public function checkVoterWithContractorThroughElection($voter_id){
        // Get the contractor and its election ID
        $con_id      = ($this->contractor_id == 0) ? $this->sheet_contractor_id : $this->contractor_id; 
        $contractor  = Contractor::withoutGlobalScopes()->find($con_id);
        if (!$contractor) {
            Log::error('Contractor not found for election check', ['contractor_id' => $con_id, 'voter_id' => $voter_id]);
            return 0;
        }
        $election_id = $contractor->election_id;
        if (!$election_id) {
            Log::error('Contractor has no election_id', ['contractor_id' => $con_id]);
            $this->not_allowed_count++;
            return 0;
        }
        
        // Simple direct query to check if voter exists in election_voter
        $exists = DB::table('election_voter')
            ->where('election_id', $election_id)
            ->where('voter_id', $voter_id)
            ->exists();
        Log::info('----------------------------::::::::');
        Log::info("Checking voter $voter_id for election $election_id: " . ($exists ? 'Found' : 'Not Found'));
        Log::info("exists : " . $exists );
        
        return $exists ? 1 : 0;
    }
    //=================================================================================================
    // public function checkVoterWithContractorThroughElection($voter_id){
    
    //     $contractor = Contractor::find($this->contractor_id);
    //     $election_id=$contractor->election_id;
        
    //     $elction=Election::find($election_id);
    //     $allowed_voters = $elction->voters()
    //     ->wherePivot('election_id', $election_id)
    //     ->pluck('voter_id');
    //     Log::info('----------------------------');
    //     // Log::info(json_encode($allowed_voters));
    //     ('----------------------------');
        
    //     $allowed_voters2 =DB::table('election_voter')->where('election_id', $election_id)->pluck('voter_id')->toArray();
        
    //     Log::info('----------------------------');
    //     // Log::info(json_encode($allowed_voters2));
    //     ('----------------------------');
        
    //     Log::info('voter_id');
    //     Log::info($voter_id);
    //     Log::info('----------------------------');
    //     Log::info($allowed_voters->contains($voter_id));
    //     Log::info('----------------------------');
        
    //     Log::info(in_array($voter_id,$allowed_voters2));
    //     Log::info('----------------------------');
    //     return $allowed_voters->contains($voter_id) ? 1 : 0;
    // }
    //=================================================================================================
    public function addVoterToContractor($voter_id,$loop=false){
        // $status='success';   
        $status     = '';
        $con_id     = ($this->contractor_id == 0)?$this->sheet_contractor_id:$this->contractor_id; 
        
        $contractor = Contractor::withoutGlobalScopes()->find($con_id);
        if (!$contractor) {
            Log::error('Contractor not found for addVoterToContractor', ['contractor_id' => $con_id, 'voter_id' => $voter_id]);
            $this->failed_count++;
            $this->contractor_not_found_count++;
            return $status;
        }
        
        Log::info('---------line 170-------------------');
        Log::info(json_encode($contractor));
        Log::info('----------------------------');
        
        $isInVoters         = $contractor->voters()->where('voter_id', $voter_id)->exists();
        $isInSoftDeletes    = $contractor->softDelete()->where('voter_id', $voter_id)->exists();
        
        if ($isInSoftDeletes) {
            $restore_data = $contractor->softDelete()->where('voter_id', $voter_id)->delete();
            if ($restore_data) {
                Log::info('restore soft delete relationship for voter', ['contractor_id' => $con_id, 'voter_id' => $voter_id]);
                $this->success_count++;
                return 'success';
            }

            $this->failed_count++;
            Log::warning('failed to restore voter from soft delete', ['contractor_id' => $con_id, 'voter_id' => $voter_id]);
        }

        if (!$isInVoters) {//voter not found with contractor --> add voter with contractor
            Log::info('---------line 194-------------------');
            Log::info('-----voter not found with contractor before------------------');
            try {
                $add_voter_to_contract = ContractorVoter::create([
                    'contractor_id'     => $con_id,
                    'voter_id'          => $voter_id,
                    'percentage'        => 0 
                ]);
                Log::info('-----after add query line 201------------------');

                if ($add_voter_to_contract) {
                    Log::info('add voter to contractor done');
                    $this->success_count++;
                    $status = 'success';
                } else {
                    $this->failed_count++;
                    Log::warning('failed to add voter to contractor, create returned falsy result', ['contractor_id' => $con_id, 'voter_id' => $voter_id]);
                }
            } catch (\Throwable $e) {
                $this->failed_count++;
                Log::error('ContractorVoter create failed', [
                    'contractor_id' => $con_id,
                    'voter_id'      => $voter_id,
                    'error'         => $e->getMessage(),
                    'trace'         => $e->getTraceAsString(),
                ]);
                $status = 'failed';
            }
        } else {//voter already found with contractor
            Log::info('---------line 214-------------------');
            Log::info('-----voter found with contractor before------------------');

            Log::info('voter already exist');
            $this->repeat_count++;
            $status = 'repeat';
        }
        return $status;
    }
    //=================================================================================================
    private function recordFailedRow(array $rowData, string $reason, array $details = []): void
    {
        $this->failedRows[] = array_merge([
            'reason' => $reason,
            'row' => $rowData,
        ], $details);
    }

    public function oldAddVoterToContractor($voter_id){
        $check_found_before=ContractorVoter::where([
            'contractor_id'     => $this->contractor_id,
            'voter_id'          => $voter_id
        ])->count();
        
        if($check_found_before>0){
            Log::info('voter already exist');
            $this->repeat_count++;
        }else{
            Log::info('voter not exist');
            $add_voter_to_contract=ContractorVoter::create([
                'contractor_id'     => $this->contractor_id,
                'voter_id'          => $voter_id,
                'percentage'        => 0 
            ]);
            if($add_voter_to_contract){
                $this->success_count++;
            }else{
                $this->failed_count++;
                Log::info('failed to add voter to contractor');
            }
        }
        return 1;
    }
    //=================================================================================================
    public function handleAddLogic($voter, $loop = false, array $rowData = []){
        $status_msg = '';
        $con_id     = ($this->contractor_id == 0) ? $this->sheet_contractor_id : $this->contractor_id; 

        Log::info("Attempting to add voter {$voter->id} to contractor {$con_id}");
        Log::info('voter id: '.$voter->id);
        Log::info('contractor id : '.$con_id);
        //=====================================================
        Log::info('---------line 248: before add operation-------------------');
        $status_msg = $this->addVoterToContractor($voter->id, $loop);
        Log::info('---------line 250: after add operation-------------------');
        if ($status_msg === 'failed' && ! empty($rowData)) {
            $this->recordFailedRow($rowData, 'db_error', ['voter_id' => $voter->id, 'contractor_id' => $con_id]);
        }
        //=====================================================

        return $status_msg;
    }
    //=================================================================================================
    public function breakLoop($msg,$count){
        $this->msg           = $msg;
        return 1;
    }
}