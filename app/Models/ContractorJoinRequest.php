<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorJoinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'requester_user_id',
        'requester_name',
        'requester_phone',
        'status',
        'decision_note',
        'decision_at',
        'decided_by_user_id',
    ];

    protected $casts = [
        'decision_at' => 'datetime',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by_user_id');
    }
}
