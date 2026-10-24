<?php

namespace App\Repositories;
use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;


class ExpenseRepository
{
     public function getPaginatedForGym(
        int $gymId,
        array $filters
    ): LengthAwarePaginator {
        return Expense::query()
            ->where('gym_id', $gymId)
            ->when(
                isset($filters['category']),
                fn ($query) => $query->where(
                    'category',
                    $filters['category']
                )
            )
            ->when(
                isset($filters['from']),
                fn ($query) => $query->whereDate(
                    'expense_date',
                    '>=',
                    $filters['from']
                )
            )
            ->when(
                isset($filters['to']),
                fn ($query) => $query->whereDate(
                    'expense_date',
                    '<=',
                    $filters['to']
                )
            )
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
    }

    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    public function getMonthlySummary(int $gymId, string $month): array
{
    $results = Expense::query()
        ->where('gym_id', $gymId)
        ->whereBetween('expense_date', [
            $month . '-01',
            \Carbon\Carbon::createFromFormat('Y-m', $month)
                ->endOfMonth()
                ->toDateString(),
        ])
        ->select('category', DB::raw('SUM(amount) as total'))
        ->groupBy('category')
        ->get();

    return [
        'month' => $month,
        'total_expenses' => (float) $results->sum('total'),
        'categories' => $results,
    ];
}
}
