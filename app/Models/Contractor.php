<?php

namespace App\Models;

use App\Models\Scopes\CreatorScope;
use App\Scopes\ElectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Foundation\Auth\User as Authenticatable;






class Contractor extends Authenticatable
{
    use HasFactory , HasPermissions ,HasRoles ,LogsActivity;
    protected $guard_name = 'web';  // Set the guard name explicitly to 'web'


    protected $fillable=[
        'parent_id',
        'user_id',
        'election_id',
        'note',
        'trust',
        'status',
        'token',
        'creator_id',
        'name',
        'email',
        'phone',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ElectionScope);
        static::addGlobalScope(new CreatorScope);

    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault(function (User $user, Contractor $contractor) {
            $user->name = $contractor->name;
            $user->email = $contractor->email;
            $user->phone = $contractor->phone;
        });
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class, 'election_id');
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
   public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function childs(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
    public function scopeChildren(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    public function voters()
    {
        return $this->belongsToMany(Voter::class)->withPivot('percentage')->withTimestamps();
    }
    public function softDelete()
    {
        return $this->belongsToMany(Voter::class,'contractor_voter_delete')->withTimestamps();
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'contractor_id');
    }
    public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName($this->table)
        ->setDescriptionForEvent(function (string $eventName) {
            $userName = auth()->user()->name ?? 'مستخدم غير معروف';

            $eventTranslations = [
                'created' => 'إنشاء مستخدم',
                'updated' => 'تعديل مستخدم',
                'deleted' => 'حذف مستخدم',
            ];

            $event = $eventTranslations[$eventName] ?? $eventName;

            return "{$event} بواسطة {$userName}";
        })
        ->logFillable()
        ->logOnlyDirty();
}

}
