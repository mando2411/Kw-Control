<?php

namespace App\Models;

use App\Scopes\ElectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'election_id',
        'max_contractor',
        'max_represent',
        'votes',
        'banner',
        'candidate_type',
        'list_candidates_count',
        'list_name',
        'list_logo',
        'is_actual_list_candidate',
        'list_leader_candidate_id',
    ];

    protected $casts = [
        'is_actual_list_candidate' => 'boolean',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new ElectionScope);
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class, 'election_id');
    }
    public function committees()
    {
        return $this->belongsToMany(Committee::class)->withPivot('votes')->withTimestamps();
    }

    public function contractorJoinRequests()
    {
        return $this->hasMany(ContractorJoinRequest::class);
    }

    public function listLeader(): BelongsTo
    {
        return $this->belongsTo(self::class, 'list_leader_candidate_id');
    }

    public function listMembers()
    {
        return $this->hasMany(self::class, 'list_leader_candidate_id');
    }

    public function isListLeader(): bool
    {
        return (string) $this->candidate_type === 'list_leader';
    }

    protected static function boot()
       {
           parent::boot();

           static::created(function ($candidate) {
               $committees = Committee::all();
               $candidate->committees()->attach($committees);
           });
           static::deleting(function ($candidate) {
               // Detach committees
               $candidate->committees()->detach();
           });
       }
}
