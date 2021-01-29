<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchRankCategory extends Model
{
    use HasFactory,Uuid ;
    protected $table = 'match_rank_categories';
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;
    public function MatchSingleDoubles()
    {
        return $this->belongsTo(MatchSingleDoubles::class,'match_single_doubles_id');
    }
}
