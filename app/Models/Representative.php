<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Representative extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'election_id',
        'committee_id',
        'status',
        'attendance'
    ];
    protected $casts = [
        'enabled' => 'boolean',
        'featured' => 'boolean',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class, 'election_id');
    }
    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class, 'committee_id');
    }
}
