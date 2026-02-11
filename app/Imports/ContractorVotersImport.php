<?php

namespace App\Imports;

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
    private $msg;
    //=================================================================================================
    public function __construct($contractor_id){
        $this->contractor_id        = $contractor_id;
        $this->sheet_contractor_id  = 0;
        $this->msg                  = '';
    }
    //=================================================================================================
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows){
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                Log::info('----------------------------');
                Log::info($row);
                Log::info('----------------------------');
                //=============================================================================================================
                if (($this->contractor_id) == 0) {
                    $this->breakLoop('يرجى اختيار المتعهد الفرعي من النموذج قبل الرفع.', 0);
                    break;
                }
                //=============================================================================================================
                // if(isset($row['alrkm_almdn']) && $row['alasm']){
                if(isset($row['alrkm_almdn'])){
                    Log::info($row['alrkm_almdn']);
                
                    $voter_detail = Voter::where(['alrkm_almd_yn'=> $row['alrkm_almdn']]);//check if the voter is already exist in the database
                    if($voter_detail->count()==0){//no voter found with this number
                        $this->failed_count++;
                        Log::info('no voter');
                    }elseif($voter_detail->count()==1){//found only one voter
                        $voter = $voter_detail->first();
                        Log::info($voter);
                        if($voter){
                            $this->handleAddLogic($voter);
                            // $this->success_count++;
                        }else{
                            // $this->failed_count++;
                            Log::info('no voter');
                        }
                    }else{//found more than one voter
                        $check_status=0;
                        foreach ($voter_detail->get() as $voter) {
                            if($this->handleAddLogic($voter,true)=='success'){
                                $check_status=1;
                                break;
                            }
                        }
                        // ($check_status==1)?$this->success_count++:$this->failed_count++;
                        if($check_status==1){$this->success_count++;}
                    }
                }else{
                    Log::warning('Missing data in row:', $row->toArray());
                    $this->breakLoop('تاكد من ادخال قيمه للرقم المدنى ', 0);
                    break;
                }
                //=============================================================================================================
                Log::info('----------------------------');
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
    //=================================================================================================
    public function checkVoterWithContractorThroughElection($voter_id){
        // Get the contractor and its election ID
        $con_id      = ($this->contractor_id == 0)?$this->sheet_contractor_id:$this->contractor_id; 
        $contractor  = Contractor::findOrFail($con_id);
        $election_id = $contractor->election_id;
        
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
        
        $contractor = Contractor::findOrFail($con_id);
        
        Log::info('---------line 170-------------------');
        Log::info(json_encode($contractor));
        Log::info('----------------------------');
        
        $isInVoters         = $contractor->voters()->where('voter_id', $voter_id)->exists();
        $isInSoftDeletes    = $contractor->softDelete()->where('voter_id', $voter_id)->exists();
        
        if ($isInSoftDeletes) {
            $restore_data=$contractor->softDelete()->where('voter_id',$voter_id)->delete();
            if($restore_data){
                Log::info('delete softedelete');
                $this->success_count++;
                $status='success';
            }else{
                $this->failed_count++;
                Log::info('failed to add voter to contractor');
            }
        }

        if (!$isInVoters) {//voter not found with contractor --> add voter with contractor
            Log::info('---------line 194-------------------');
            Log::info('-----voter not found with contractor before------------------');
            $add_voter_to_contract=ContractorVoter::create([
                'contractor_id'     => $con_id,
                'voter_id'          => $voter_id,
                'percentage'        => 0 
            ]);
            Log::info('-----after add query line 201------------------');

            if($add_voter_to_contract){
                Log::info('add voter to contractor done');
                // $this->success_count++;
                $status='success';   
            }else{
                // ($loop)?$this->failed_count++:'';
                Log::info('failed to add voter to contractor');
            }
        }else{//voter already found with contractor
            Log::info('---------line 214-------------------');
            Log::info('-----voter found with contractor before------------------');
        
            Log::info('voter already exist');
            $this->repeat_count++;
        }
        return $status;
    }
    //=================================================================================================
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
    public function  handleAddLogic($voter,$loop=false){
        $status_msg = '';
        $con_id     = ($this->contractor_id == 0)?$this->sheet_contractor_id:$this->contractor_id; 
        
        if ($this->checkVoterWithContractorThroughElection($voter->id)) {//check if this voter related with Contractor election
            Log::info("Voter {$voter->id} is allowed in election. Adding to contractor {$con_id}");
            Log::info('voter id: '.$voter->id);
            Log::info('contractor id : '.$con_id);
            //=====================================================
            Log::info('---------line 248: before add operation-------------------');
            $status_msg=$this->addVoterToContractor($voter->id,$loop);
            Log::info('---------line 250: after add operation-------------------');
            
            // $this->success_count++;
            //=====================================================
            // $this->oldAddVoterToContractor($voter->id);
            //=====================================================
        } else {
            Log::info("Voter {$voter->id} is not allowed in election for contractor {$con_id}");
            // $this->failed_count++;
        }
        return $status_msg;
    }
    //=================================================================================================
    public function breakLoop($msg,$count){
        $this->msg           = $msg;
        $this->failed_count  = $count;
        $this->success_count = 0;
        $this->repeat_count  = 0;
        return 1;
    }
}