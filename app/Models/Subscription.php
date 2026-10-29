<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'plan_id',
        'start_date',
        'end_date',
        'payment_status',
        'payment_method',
        'amount_paid',
        'paid_at',
    ];


    public function member()
    {

        return $this->belongsTo(Member::class);
    }


    public function plan()
    {

        return $this->belongsTo(Plan::class);
    }

    public function attendances()
{
    return $this->hasMany(Attendance::class);
}

    protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'amount_paid' => 'decimal:2',
    'paid_at' => 'datetime',
];
}
