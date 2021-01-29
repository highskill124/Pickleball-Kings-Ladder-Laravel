<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory,Uuid;
    protected $table = 'matches';
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;

    public function request()
    {
        return $this->belongsTo(Requests::class,'requests_id','id');
    }
}
