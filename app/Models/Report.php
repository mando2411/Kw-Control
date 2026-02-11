<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Html\Editor\Fields\BelongsTo;

class Report extends Model
{
    use HasFactory;
    protected $fillable = [
        'pdf',
        'creator_id',
    ];
    public function creator() :BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
