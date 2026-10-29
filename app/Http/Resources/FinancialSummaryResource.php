<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialSummaryResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
             
            'from' => $this['from'],
            'to' => $this['to'],
            'subscription_revenue' => $this['subscription_revenue'],
            'store_revenue' => $this['store_revenue'],
            'total_revenue' => $this['total_revenue'],
            'total_expenses' => $this['total_expenses'],
            'net_profit' => $this['net_profit'],

        ];
    }
}
