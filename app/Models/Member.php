<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'gym_id',
        'name',
        'phone',
        'email',
        'photo_url',
        'join_date',
    ];
}
