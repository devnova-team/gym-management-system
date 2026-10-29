<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [

        'gym_id',
        'name',
        'purchase_date',
        'last_maintenance_date',
        'next_maintenance_due'
    ];


    protected $casts = [
        'purchase_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_due' => 'date',
    ];


    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }


    public function getMaintenanceStatusAttribute(): ?string
    {

        if (! $this->next_maintenance_due) {
            return null;
        }

        $today = Carbon::today('Africa/Cairo');

        if ($this->next_maintenance_due->lt($today)) {

            return 'overdue';
        }

        if ($this->next_maintenance_due->lte($today->copy()->addDays(7))) {

            return 'due_soon';
        }

        return 'ok';
    }
}
