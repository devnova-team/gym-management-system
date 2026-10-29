<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity_sold' => $this->quantity_sold,
            'total_amount' => $this->total_amount,
            'sale_date' => $this->sale_date?->format('Y-m-d'),

            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'unit_price' => $this->product->unit_price,
                ];
            }),

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
