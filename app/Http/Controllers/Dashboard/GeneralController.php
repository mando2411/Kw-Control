<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Voter;
use App\Models\Committee;
use App\Models\Contractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class GeneralController extends Controller
{
    //==============================================================
    public function fetchContractorsForUser($user_id){
    // return $user_id;
        $contractors = Contractor::where(['creator_id'=>$user_id,'parent_id'=>Null])->pluck('name', 'id');
        return response()->json($contractors);
    }
    //==============================================================
    public function fetchSubContractorsForMain($main_id){
        $contractors = Contractor::where('parent_id', $main_id)->pluck('name', 'id');
        return response()->json($contractors);
    }
    //==============================================================
    public function fetchVotersForCommittee($committee_id,Request $request){
        // return response()->json(['msg'=>'success','data'=>$request->all(),'commitee'=>$committee]);
        $committee          = Committee::find($committee_id);
        $election_id        = $committee->election_id;
        $type               = ($committee->type!='اناث')?'ذكر':'انثي';
        $search_value       = $request->searchValue;    
    
        $voters = Voter::where('type',$type)->whereHas('election', function ($query) use ($election_id) {
            $query->where('election_id', $election_id);
        });
        //:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        // if ($search_value != '') {
        //     Log::info('search value :'.$search_value);
        //     $voters->where('name', 'like', $search_value . '%'); 
        // }
        //:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        $voters = $this->search2($voters,$search_value);//apply condition for first letter only
        //:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        
        $voters = $voters->orderBy('id', 'asc')
            ->limit(100)
            ->get();
        
        return response()->json(['voters'=>$voters]);
    }
    //==============================================================
    public function fetchAttendingCountForCommittee($committee_id){
        $committee          = Committee::find($committee_id);
        (auth()->user()->representatives()->exists())?$attend_count=$committee->voters()->where('status', 1)->count():$attend_count=$committee->voters()->where('status', 1)->count();
        $voter_count=counts($committee->type);
        
        return response()->json(['attend_count'=>$attend_count,'voter_count'=>$voter_count]);
    }
    //==============================================================
    public function search2($voters, $search_value) {
        if ($search_value != '') {
            Log::info('search value: ' . $search_value);
    
            // Normalized characters mapping
            $normalizedChars = [
                'أ' => ['أ', 'ا', 'إ', 'آ'],
                'ا' => ['أ', 'ا', 'إ', 'آ'],
                'إ' => ['أ', 'ا', 'إ', 'آ'],
                'آ' => ['أ', 'ا', 'إ', 'آ'],
                'ي' => ['ي', 'ى'],
                'ى' => ['ي', 'ى'],
                'ة' => ['ة', 'ه'],
                'ه' => ['ة', 'ه']
            ];
    
            // Word substitutions
            $substitutions = [
                'نجلاء' => ['نجله'],
                'بداح' => ['ابداح'],
                'ظافر' => ['ضافر'],
                'ندا' => ['نداء'],
                'سارا' => ['سارة'],
                'نورا' => ['نوره']
            ];
    
            // Extract first name and remaining words
            $keywords = explode(' ', trim($search_value));
            $firstWord = $keywords[0]; // First name must match exactly
            $remainingWords = array_slice($keywords, 1); // Other words
    
            $voters->where(function ($query) use ($firstWord, $remainingWords, $normalizedChars, $substitutions) {
                // Generate variations for the first word
                $firstWordVariations = $this->generateSearchTerms($firstWord, $normalizedChars);
    
                // Add word substitutions
                if (array_key_exists($firstWord, $substitutions)) {
                    $firstWordVariations = array_merge($firstWordVariations, $substitutions[$firstWord]);
                }
    
                // Ensure first name is EXACT match at the beginning
                $query->where(function ($subQuery) use ($firstWordVariations) {
                    foreach ($firstWordVariations as $variation) {
                        $subQuery->orWhere('name', 'LIKE', "{$variation} %");
                    }
                });
    
                // If there are additional words, ensure they appear somewhere in the name
                if (!empty($remainingWords)) {
                    foreach ($remainingWords as $word) {
                        $query->where('name', 'LIKE', "%{$word}%");
                    }
                }
            })->orWhere('alsndok', 'like', $search_value . '%');
    
            Log::info('Generated search terms: ', $keywords);
        }
        return $voters;
    }
    
    /**
     * Generate all possible combinations of a string based on normalized characters
     */
    private function generateSearchTerms($input, $normalizedChars) {
        $combinations = [''];
    
        for ($i = 0; $i < mb_strlen($input, 'UTF-8'); $i++) {
            $char = mb_substr($input, $i, 1, 'UTF-8');
            if (isset($normalizedChars[$char])) {
                $newCombinations = [];
                foreach ($combinations as $combination) {
                    foreach ($normalizedChars[$char] as $replacement) {
                        $newCombinations[] = $combination . $replacement;
                    }
                }
                $combinations = $newCombinations;
            } else {
                foreach ($combinations as &$combination) {
                    $combination .= $char;
                }
            }
        }
    
        return $combinations;
    }
       //==============================================================
}