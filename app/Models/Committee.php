<?php

namespace App\Models;

use App\Scopes\ElectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Committee extends Model
{
    use LogsActivity,HasFactory ;
    protected $fillable =[
        'name',
        'type',
        'school_id',
        'election_id',
        'status'
    ];
  

    protected static function booted()
    {
        static::addGlobalScope(new ElectionScope);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($committee) {

            // Keep committee-candidate mapping within the same election context.
            $candidateIds = Candidate::withoutGlobalScopes()
                ->where('election_id', $committee->election_id)
                ->pluck('id')
                ->toArray();

            if (!empty($candidateIds)) {
                $committee->candidates()->syncWithoutDetaching($candidateIds);
            }

            if (empty($committee->school_id)) {
                $schoolQuery = School::query()->where('type', $committee->type);
                if (Schema::hasColumn('schools', 'election_id')) {
                    $schoolQuery
                        ->where(function ($query) use ($committee) {
                            $query->where('election_id', $committee->election_id)
                                ->orWhereNull('election_id');
                        })
                        ->orderByRaw('CASE WHEN election_id = ? THEN 0 ELSE 1 END', [(int) $committee->election_id]);
                }

                $school = $schoolQuery->first();

                if ($school) {
                    $committee->update([
                        'school_id' => $school->id
                    ]);
                }
            }
        });

        static::updating(function ($committee) {
            if ($committee->isDirty('school_id')) {
                return;
            }

            if (!$committee->isDirty(['type', 'election_id'])) {
                return;
            }

            $schoolQuery = School::query()->where('type', $committee->type);
            if (Schema::hasColumn('schools', 'election_id')) {
                $schoolQuery
                    ->where(function ($query) use ($committee) {
                        $query->where('election_id', $committee->election_id)
                            ->orWhereNull('election_id');
                    })
                    ->orderByRaw('CASE WHEN election_id = ? THEN 0 ELSE 1 END', [(int) $committee->election_id]);
            }

            $school = $schoolQuery->first();

            $committee->school_id = $school?->id;
        });
    }

       public function candidates()
       {
           return $this->belongsToMany(Candidate::class, 'candidate_committee')->withPivot('votes')->withTimestamps();
       }
    public function voters(): HasMany
    {
        return $this->hasMany(Voter::class, 'committee_id');
    }

    public function representatives(): HasMany
    {
        return $this->hasMany(Representative::class, 'committee_id');
    }
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
    public function users(){
            $users = $this->representatives->map(function ($rep) {
                $user = $rep->user;

                return [
                    'id' => $rep->id,
                    'name' => $user?->name ?? 'مستخدم غير متاح',
                    'phone' => $user?->phone ?? '-',
                    'user_id' => $user?->id,
                    'committee_id' => $rep->committee_id,
                ];
            });
return $users;
    }
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class, 'election_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName($this->table)
            ->logFillable()
            ->logOnlyDirty();
    }


}
