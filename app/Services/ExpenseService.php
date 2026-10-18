<?php

namespace App\Services;

use App\Models\Expense;

use App\Repositories\ExpenseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class ExpenseService
{
    
    public function __construct(
        ExpenseRepository $expenseRepository
    )
    {
        $this->expenseRepository = $expenseRepository;
    }

    public function getExpenses(int $gymId,array $filters): LengthAwarePaginator {
        return $this->expenseRepository->getPaginatedForGym(
            $gymId,
            $filters
        );
    }

    public function createExpense(int $gymId , array $data): Expense {
        $data = array_merge($data, [
            'gym_id' => $gymId,
        ]);

        return $this->expenseRepository->create($data);
    }

    public function getMonthlySummary(int $gymId, string $month): array{
    return $this->expenseRepository->getMonthlySummary(
        $gymId,
        $month
    );
   }

}
