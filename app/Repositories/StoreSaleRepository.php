<?php

namespace App\Repositories;
use App\Models\StoreProduct;
use App\Models\StoreSales;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class StoreSaleRepository
{
    public function findProductForUpdate(
        int $gymId,
        int $productId
    ): StoreProduct
    {
        return StoreProduct::query()
        ->where('gym_id',$gymId)
        ->whereKey($productId)
        ->lockForUpdate()
        ->firstOrFail();
    }

    public function createSale(array $data): StoreSales
    {
        return StoreSales::create($data);
    }

    public function decreaseStock(StoreProduct $product,int $quantity): void
     {
        $product->quantity -= $quantity;
        $product->save();
    }

    private function salesQuery(int $gymId, array $filters): Builder 
    {
        return StoreSales::query()
            ->where('gym_id', $gymId)
            ->when(
                $filters['from'] ?? null,
                fn ($query, $from) =>
                    $query->whereDate('sale_date', '>=', $from)
            )
            ->when(
                $filters['to'] ?? null,
                fn ($query, $to) =>
                    $query->whereDate('sale_date', '<=', $to)
            );
    }

    public function index(int $gymId, array $filters): LengthAwarePaginator 
    {
        return $this->salesQuery($gymId, $filters)
            ->with('product:id,name,unit_price')
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }
}
