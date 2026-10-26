<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Gym;

class Plan extends Model
{
    use HasFactory;


    protected $fillable = [
        'gym_id',
        'name',
        'type',
        'duration_days',
        'total_sessions',
        'price',
        'absence_threshold_days',
    ];


    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }


    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    protected function casts(): array
{
    return [
        'duration_days' => 'integer',
        'total_sessions' => 'integer',
        'price' => 'decimal:2',
        'absence_threshold_days' => 'integer',
    ];
}
}
