<?php

namespace App\Models;

use App\Scopes\ElectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pipeline\Pipeline;

class Voter extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'type',
        'almrgaa',
        'albtn',
        'alfraa',
        'yearOfBirth',
        'btn_almoyhy',
        'tary_kh_alandmam',
        'alrkm_ala_yl_llaanoan',
        'alktaa',
        'alrkm_almd_yn',
        'alsndok',
        'phone1',
        'region',
        'status',
        'committee_id',
        'family_id',
        'alfkhd',
        'phone2',
        'cod1',
        'cod2',
        'cod3',
        'father',
        'age',
        'user_id',
        'grand',
        'restricted',
        'attend_id',
        'normalized_name'
    ];
    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function booted()
{
    static::addGlobalScope(new ElectionScope);
}

        public function contractors()
        {
            return $this->belongsToMany(Contractor::class)
                ->withPivot('percentage')
                ->withTimestamps();
        }
    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class, 'committee_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function attend(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attend_id');
    }
    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id');
    }
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_voter');
    }
        public function election(): BelongsToMany
        {
            return $this->belongsToMany(Election::class, 'election_voter');
        }

    public static function FilterQuery(){
        return app(Pipeline::class)
        ->send(Voter::query())
        ->through([
            \App\Filters\Name::class,
            \App\Filters\First_Name::class,
            \App\Filters\Elbtn::class,
            \App\Filters\Phone::class,
            \App\Filters\Family::class,
            \App\Filters\Committee::class,
            \App\Filters\Search::class,
            \App\Filters\Second_Name::class,
            \App\Filters\Siblings::class,
            \App\Filters\Status::class,
            \App\Filters\Third_Name::class,
            \App\Filters\Elfa5z::class,
            \App\Filters\Alktaa::class,
            \App\Filters\Elfar3::class,
            \App\Filters\Fourth_Name::class,
            \App\Filters\Cod1::class,
            \App\Filters\Cod2::class,
            \App\Filters\Cod3::class,
            \App\Filters\Type::class,
            \App\Filters\ID::class,
            \App\Filters\Box::class,
            \App\Filters\Age::class,
            \App\Filters\Restricted::class,

        ])
        ->thenReturn()
        ->orderBy('name',"asc")
            ->with('family','contractors');
    }

    public static function Filter(){
        return static::FilterQuery()->get();
    }

    public static function Madamen(){
        $query = Voter::query();
        $query->whereHas('contractors');
        $voters = app(Pipeline::class)
            ->send($query)
            ->through([
                \App\Filters\Mot3ahed::class,
                \App\Filters\Committee::class,
                \App\Filters\Date::class,

            ])
            ->thenReturn();
        return $voters;
    }


}
