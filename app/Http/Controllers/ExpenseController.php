<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Expense\IndexExpenseRequest;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\MonthlyExpenseSummaryRequest;
use App\Http\Resources\ExpenseResource;
use App\Services\ExpenseService;



class ExpenseController extends Controller
{

    public function __construct(
         ExpenseService $expenseService
    ) {
        $this->expenseService = $expenseService;
    }
        /**
     * Display expenses belonging to the authenticated gym.
     */


    public function index(IndexExpenseRequest $request)
    {
        $user = $request->user();

        $expenses = $this->expenseService->getExpenses(
            (int) $user->gym_id,
            $request->validated()
        );

        return ExpenseResource::collection($expenses)
            ->additional([
                'success' => true,
                'message' => 'Expenses retrieved successfully.',
        ]);
    }

    /**
     * Store a new expense for the authenticated gym.
     */

    public function store(StoreExpenseRequest $request)
    {
        $user = $request->user();

        $expense = $this->expenseService->createExpense(
            (int) $user->gym_id,
            $request->validated()
        );

        return (new ExpenseResource($expense))
            ->additional([
                'success' => true,
                'message' => 'Expense created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function monthlySummary(MonthlyExpenseSummaryRequest $request){
        
    $user = $request->user();

    $summary = $this->expenseService->getMonthlySummary(
        (int) $user->gym_id,
        $request->validated()['month']
    );

    return response()->json([
        'success' => true,
        'message' => 'Monthly expenses retrieved successfully.',
        'data' => $summary,
    ]);
}

}
