<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selection extends Model
{
    use HasFactory;
    protected $fillable=[
        'cod1',
        'cod2',
        'cod3',
        'alfkhd',
        'alktaa',
        'albtn',
        'alfraa',
        'street',
        'home',
        'alharaa',
    ];
}
