<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid as UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLinks extends Model
{
    use HasFactory,UUID;
    protected $table = 'social_links';
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;

}
