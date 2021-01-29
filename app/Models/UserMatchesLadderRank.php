<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMatchesLadderRank extends Model
{
    use HasFactory,Uuid;
    protected $table = 'user_matches_ladder_ranks';
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
