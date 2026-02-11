<?php

namespace App\Models;

use App\Scopes\ElectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

            $candidateIds = Candidate::pluck('id')->toArray();

            // Debug line
            \Log::info('Candidate IDs:', $candidateIds);

            if (!empty($candidateIds)) {
                $committee->candidates()->attach($candidateIds);
            }

            $school=School::where('type',$committee->type)->first();
            if ($school) {
                # code...
                $committee->update([
                    'school_id'=>$school->id
                ]);
            }
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
                return [
                    'id' => $rep->id,
                    'name' => $rep->user->name,
                    'phone' => $rep->user->phone,
                    'user_id' => $rep->user->id,
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
