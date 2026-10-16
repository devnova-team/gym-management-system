<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [

        'staff_id',
        'shift_date',
        'start_time',
        'end_time'
    ];


    protected $casts = [
        'shift_date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
