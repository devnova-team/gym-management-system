<?php

namespace App\Services;
use App\Models\StoreProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\StoreProductRepository;

class StoreProductService
{
     public function __construct(
       private StoreProductRepository $storeProductRepository
    )
    {
    }


   public function index(int $gymId): Collection
   {
      return $this->storeProductRepository->index(
        $gymId,
      );
   }

   public function store(int $gymId , array $data): StoreProduct
   {
      return $this->storeProductRepository->store(
        $gymId,
        $data,
      );
   }

   public function show(int $gymId , int $id): StoreProduct
   {
       return $this->storeProductRepository->show(
        $gymId,
        $id,
       );
   }

       public function update(int $gymId, int $id, array $data ): StoreProduct 
       {
       

        return $this->storeProductRepository->update(
            $gymId,
            $id,
            $data,
        );
    }

    public function destroy(int $gymId, int $id): void
    {
        $product = $this->storeProductRepository->show(
            $gymId,
            $id
        );

        // Prevent deleting products with sales history
        if ($product->sales()->exists()) {
            throw new \DomainException(
                'Cannot delete a product that has sales history.'
            );
        }

          $this->storeProductRepository->destroy($product);
       
    }
}
