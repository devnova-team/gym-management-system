<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Store\StoreSaleRequest;
use App\Http\Requests\Store\StoreSalesFilterRequest;
use  App\Services\StoreSaleService;
use App\Models\StoreProduct;
use App\Models\StoreSales;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DomainException;
use App\Http\Resources\StoreSaleResource;

class StoreSaleController extends Controller
{
     public function __construct(
        private StoreSaleService $storeSaleService
     )
     {
     }

      public function index(StoreSalesFilterRequest $request ): JsonResponse
       {
        $sales = $this->storeSaleService->index(
            (int) $request->user()->gym_id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Sales retrieved successfully.',
            'data' => StoreSaleResource::collection($sales),
        ]);
    }

      public function store(StoreSaleRequest $request): JsonResponse
       {
        try {
         $sale = $this->storeSaleService->store(
                (int) $request->user()->gym_id,
                $request->validated()
            );

         return response()->json([
                'success' => true,
                'message' => 'Sale recorded successfully.',
               'data' => new StoreSaleResource($sale)
            ], 201);

        } catch (DomainException $e) {
            return response()->json([
               'success' => false,
               'message' => $e->getMessage(),
            ], 409);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }
    }
}
