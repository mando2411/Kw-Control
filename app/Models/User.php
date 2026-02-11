<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'last_login_at',
        'last_active_at',
        'creator_id',
        'election_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'last_login_at' => 'datetime',

    ];


    /**
     * Get the user's first name.
     *
     * @return Attribute
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => Str::of($attributes['name'])->upper()->explode(' ')[0],
        );
    }


    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'created_by_id');
    }
    public function representatives(): HasMany
    {
        return $this->hasMany(Representative::class, 'user_id');
    }
    public function candidate(): HasMany
    {
        return $this->hasMany(Candidate::class, 'user_id');
    }
    public function contractors(): HasMany
    {
        return $this->hasMany(Contractor::class, 'creator_id');
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(self::class, 'creator_id');
    }
     public function contractor()
    {
        return $this->hasOne(Contractor::class,'user_id');
    }
    public function isOnline()
{
    return $this->last_active_at && $this->last_active_at->gt(now()->subMinutes(5));
}
public function isOffline()
{
    return $this->last_active_at && $this->last_active_at->lte(now()->subMinutes(5)) && $this->last_active_at->gt(now()->subHour());
}

public function LoginTime($lastLogin)
{
    if($lastLogin){

    Carbon::setLocale('ar');
    $lastLogin = Carbon::parse($lastLogin)->translatedFormat('Y/m/d') . ' ' .
        Carbon::parse($lastLogin)->dayName . ' من  ' ." ".
        Carbon::parse($lastLogin)->format('h') . ' :: ' .
        Carbon::parse($lastLogin)->format('i') . ' :: '  .
        Carbon::parse($lastLogin)->format('s') ;
    }
    return $lastLogin;
}

public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

}
