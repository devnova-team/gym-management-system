<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminReminder extends Model
{
    protected $fillable = [
        'gym_id',
        'title',
        'due_date',
        'reminder_note',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

     public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }
}
