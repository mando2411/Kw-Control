<?php

namespace App\Services\Query;

use App\Enums\Type;
use App\Models\Committee;
use Illuminate\Support\Facades\DB;

class CommitteeGenerator
{
    public function createRecords($data)
    {
        
        // Loop for men committees
        for ($i = 1; $i <= $data['men']; $i++) {
            Committee::create([
                'name' => $i . ' لجنه',
                'type' => Type::MEN->value,
                'election_id' => $data['election_id']
            ]);
        }
    
        // Loop for women committees
        for ($i = 1; $i <= $data['women']; $i++) {
            Committee::create([
                'name' => $i . ' لجنه',
                'type' => Type::WOMEN->value,
                'election_id' => $data['election_id']
            ]);
        }
    
        return 'تمت اضافه' . " " . 'عدد لجان' . " " . ($data['men'] + $data['women']);
    }
    
}
