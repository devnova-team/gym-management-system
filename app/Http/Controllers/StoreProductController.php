<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Store\StoreStoreProductRequest;
use App\Http\Requests\Store\UpdateStoreProductRequest;
use App\Services\StoreProductService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\StoreProductResource;


class StoreProductController extends Controller
{
    public function __construct(
        private StoreProductService $storeProductService
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->storeProductService->index(
            (int) $request->user()->gym_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'data' => StoreProductResource::collection($products),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreProductRequest $request): JsonResponse
    {
        $product = $this->storeProductService->store(
            (int) $request->user()->gym_id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => new StoreProductResource($product),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request , int $id): JsonResponse
    {
        $product = $this->storeProductService->show(
            (int) $request->user()->gym_id,
            $id
        );

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully.',
            'data' =>  new StoreProductResource($product),
        ]);
    }

 
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreProductRequest $request, int $id): JsonResponse 
    {
        $product = $this->storeProductService->update(
            (int) $request->user()->gym_id,
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => new StoreProductResource($product),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,int $id): JsonResponse 
    {
        try {
            $this->storeProductService->destroy(
                (int) $request->user()->gym_id,
                $id
            );
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
