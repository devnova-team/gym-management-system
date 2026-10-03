<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Member\StoreRequest;
use App\Http\Requests\Member\UpdateRequest;
use App\Services\MemberService;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{

    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }



    public function index(Request $request)
    {
        $gym_id = $request->user()->gym_id;

        $searchItem = $request->input('search');

        $members = $this->memberService->getMembers($gym_id, $searchItem);

        return ApiResponse::success([
            'members' => MemberResource::collection($members)
        ], 'Members retrieved successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $gym_id = $request->user()->gym_id;

        $member = $this->memberService->getMemberById($gym_id, $id);

        return ApiResponse::success([
            'member' => new MemberResource($member)
        ], 'Member retrieved successfully');
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

        $gym_id = $request->user()->gym_id;
        $data = $request->validated();


        $member = $this->memberService->createMember($gym_id, $data);

        return ApiResponse::success([
            'member' => new MemberResource($member)
        ], 'Member was created successfully');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $gym_id = $request->user()->gym_id;
        $data = $request->validated();


        $member = $this->memberService->updateMember($gym_id, $id, $data);

        return ApiResponse::success([
            'member' => new MemberResource($member)
        ], 'Member updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $gym_id = $request->user()->gym_id;

        $this->memberService->deleteMember($gym_id, $id);

        return ApiResponse::success(null, 'Member deleted successfully');
    }
}
