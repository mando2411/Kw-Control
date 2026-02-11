<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class ContractorVoter extends Model
{
    use HasFactory ,LogsActivity;
    protected $table = 'contractor_voter';
    protected $guard_name = 'web';  // Set the guard name explicitly to 'web'
    protected $fillable=[ 'contractor_id','voter_id', 'percentage', ];
    //=================================================================================================
    public function contractor(): BelongsTo{
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }
    //=================================================================================================
    public function voter(): BelongsTo{
        return $this->belongsTo(Voter::class, 'voter_id');
    }
    //=================================================================================================
    public function getActivitylogOptions(): LogOptions{
        return LogOptions::defaults()
            ->useLogName($this->table)
            ->setDescriptionForEvent(function (string $eventName) {
                $userName = auth()->user()->name ?? 'مستخدم غير معروف';
    
                $eventTranslations = [
                    'created' => 'اضافه ناخب',
                ];
    
                $event = $eventTranslations[$eventName] ?? $eventName;
    
                return "{$event} بواسطة {$userName}";
            })
            ->logFillable()
            ->logOnlyDirty();
    }
    //=================================================================================================
}