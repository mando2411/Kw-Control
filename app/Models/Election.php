<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'type',
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
];

public function scopeForUser($query, $userId)
{
    return $query->whereHas('users', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    });
}

public function voters()
{
    return $this->belongsToMany(Voter::class, 'election_voter');
}

public function candidates()
{
    return $this->hasMany(Candidate::class);
}

public function users()
{
    return $this->hasMany(User::class);
}
public function committees()
{
    return $this->hasMany(Committee::class);
}



}
