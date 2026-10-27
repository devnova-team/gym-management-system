<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Equipment\StoreRequest;
use App\Http\Requests\Equipment\UpdateRequest;
use App\Http\Resources\EquipmentResource;
use App\Services\EquipmentService;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    protected EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {

        $this->equipmentService = $equipmentService;
    }

    public function index(Request $request)
    {

        $gym_id = $request->user()->gym_id;

        $equipment = $this->equipmentService->getEquipment($gym_id);

        return ApiResponse::success([
            'equipment' => EquipmentResource::collection($equipment)
        ], 'Equipment retrieved successfully');
    }


    public function store(StoreRequest $request)
    {

        $gym_id = $request->user()->gym_id;
        $data = $request->validated();

        $equipment = $this->equipmentService->createEquipment($gym_id, $data);

        return ApiResponse::success([
            'equipment' => new EquipmentResource($equipment)
        ], 'Equipment created successfully');
    }


    public function update(UpdateRequest $request, int $id)
    {

        $gym_id = $request->user()->gym_id;
        $data = $request->validated();

        $equipment = $this->equipmentService->updateEquipment($gym_id, $id, $data);

        return ApiResponse::success([
            'equipment' => new EquipmentResource($equipment)
        ], 'Equipment updated successfully');
    }
}
