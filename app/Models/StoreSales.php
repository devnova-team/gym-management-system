<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class StoreSales extends Model
{
    protected $fillable =[
        'gym_id',
        'product_id',
        'quantity_sold',
        'total_amount',
        'sale_date',
    ];

        protected function casts(): array
    {
        return [
            'quantity_sold' => 'integer',
            'total_amount' => 'decimal:2',
            'sale_date' => 'date:Y-m-d',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function product(): BelongsTo 
    {
        return $this->belongsTo(StoreProduct::class ,  'product_id');
    }

    
}
