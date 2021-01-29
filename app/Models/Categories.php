<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory,Uuid;
    protected $table = 'categories';
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;
    public function User()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
