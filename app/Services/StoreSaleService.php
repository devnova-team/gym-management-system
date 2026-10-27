<?php

namespace App\Services;
use App\Repositories\StoreSaleRepository;
use App\Models\StoreProduct;
use App\Models\StoreSales;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use DomainException;

class StoreSaleService
{
    
    public function __construct(
        private StoreSaleRepository $storeSaleRepository
    )
    {
        //
    }

    public function store(int $gymId , array $data ): StoreSales
    {
        return DB::transaction(function () use (
            $gymId,
            $data,
        ) 
        {
            $product = $this->storeSaleRepository->findProductForUpdate(
                $gymId,
                (int) $data['product_id']
            ); 



            $quantity = (int) $data['quantity_sold'];

            if ($product->quantity < $quantity) {
                throw new DomainException(
                    'Insufficient stock.'
                );
            }

            $totalAmount = bcmul(
                (string) $product->unit_price,
                (string) $quantity,
                2
            );

            $sale = $this->storeSaleRepository->createSale([
                'gym_id' => $gymId,
                'product_id' => $product->id,
                'quantity_sold' => $quantity,
                'total_amount' => $totalAmount,
                'sale_date' => now()->toDateString(),
            ]);

            $this->storeSaleRepository->decreaseStock(
                $product,
                $quantity
            );

            return $sale->load(
                'product:id,name,unit_price'
            );
    
        }, 3);
    }

    public function index(int $gymId, array $filters): LengthAwarePaginator 
    {
        return $this->storeSaleRepository->index(
            $gymId,
            $filters
        );
    }
}
