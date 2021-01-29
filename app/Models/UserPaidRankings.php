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
        'id',
    ];

    public $incrementing = false;
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function MatchRankCategories()
    {
        return $this->belongsTo(MatchRankCategory::class,'match_rank_categories_id');
    }
}
