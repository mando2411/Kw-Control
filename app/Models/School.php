<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


class School extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'type'
    ];

    public function committees(): HasMany
    {
        return $this->hasMany(Committee::class);
    }
    public function men()
    {
        return $this->votersByGender('ذكور');
    }

    public function women()
    {
        return $this->votersByGender('اناث');
    }

    public function votersByGender($gender)
    {
        return $this->committees()
            ->join('voters', 'committees.id', '=', 'voters.committee_id')
            ->where('committees.type', $gender)
            ->count('voters.id');
    }
    public function voters(): HasManyThrough
    {
        return $this->hasManyThrough(Voter::class, Committee::class);
    }
}
