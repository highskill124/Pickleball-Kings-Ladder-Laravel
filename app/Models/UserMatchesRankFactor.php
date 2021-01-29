<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMatchesRankFactor extends Model
{
    use HasFactory,Uuid;
    protected $table = 'user_matches_rank_factors';
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function match()
    {
        return $this->belongsTo(Matches::class,'matches_id');
    }
}
