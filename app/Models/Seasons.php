<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seasons extends Model
{
    use HasFactory,Uuid;
    protected $table = 'seasons';
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;

    public function matchType(){
        return $this->belongsTo(MatchSingleDoubles::class,"match_single_doubles_id");
    }
}
