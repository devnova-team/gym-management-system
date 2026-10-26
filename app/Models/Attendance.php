<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
     use HasFactory;

 protected $table = 'attendance';

    protected $fillable = [
        'member_id',
        'subscription_id',
        'check_in_time',
        'check_out_time',
        'attendance_date',
        'local_id',
    ];

      protected function casts(): array
    {
        return [
            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
            'attendance_date' => 'date',
        ];
    }

      public function member()
     {
        return $this->belongsTo(Member::class);
     }

     public function subscription()
{
    return $this->belongsTo(Subscription::class);
}
}
