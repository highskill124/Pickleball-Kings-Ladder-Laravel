<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaidRankings extends Model
{
    use HasFactory,Uuid;
    protected $table = 'user_paid_rankings';
    protected $fillable = [
        'id','user_id', 'match_ladder_id'
    ];

    public $incrementing = false;
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function MatchLadder()
    {
        return $this->belongsTo(MatchLadders::class,'match_ladder_id');
    }
}
