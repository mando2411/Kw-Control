<?php

namespace App\Models;

use App\Scopes\ElectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;
     protected $fillable =[
        'name',
        'election_id'
     ];
     protected static function booted()
     {
         static::addGlobalScope(new ElectionScope);
     }
     public function election(): BelongsTo
     {
        return $this->belongsTo(Election::class);
     }
     public function voters(): HasMany
     {
         return $this->hasMany(Voter::class, 'family_id');
     }
}
