<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    use HasFactory,Uuid;
    protected $table = 'requests';
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;

    public function to()
    {
        return $this->belongsTo(User::class,"request_to");
    }
    public function by()
    {
        return $this->belongsTo(User::class,"request_by");
    }
    public function match()
    {
        return $this->belongsTo(Matches::class,'matches_id');
    }
    // public function industries()
    // {
    //     return $this->belongsTo(User::class, "industry");
    // }
}
