<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchLadders extends Model
{
    use HasFactory,Uuid;
    protected $table = 'match_ladders';
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;
}
