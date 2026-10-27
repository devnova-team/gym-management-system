<?php

namespace App\Repositories;
use App\Models\StoreProduct;
use Illuminate\Database\Eloquent\Collection;



class StoreProductRepository
{
    public function index(int $gymId): Collection
    {
        return StoreProduct::query()->where('gym_id', $gymId)->latest()->get();
    }

    public function store(int $gymId , array $data): StoreProduct
    {
        return StoreProduct::create([
        ...$data,
        'gym_id'=> $gymId,
      ]);
    }

    public function show(int $gymId, int $id): StoreProduct
    {
        return StoreProduct::query()->where('gym_id', $gymId)->findOrFail($id);
    }

    public function update(int $gymId, int $id, array $data): StoreProduct 
    {
        $product = $this->show($gymId, $id);

        $product->update($data);

        return $product->refresh();
    }

    public function destroy(StoreProduct $product): void
    {
        

        $product->delete();
    }
}
