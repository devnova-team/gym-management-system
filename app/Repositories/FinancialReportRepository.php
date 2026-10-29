<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\Expense;
use App\Models\StoreSales;

class FinancialReportRepository
{
    
    public function getSubscriptionRevenue(
        int $gymId,
        string $fromUtc,
        string $toUtc
    ): string {
        return (string) Subscription::query()
            ->whereHas('member', function ($query) use ($gymId) {
                $query->where('gym_id', $gymId);
            })
            ->where('payment_status', 'paid')
            ->whereNotNull('amount_paid')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$fromUtc, $toUtc])
            ->sum('amount_paid');
    }

    public function getStoreRevenue(
        int $gymId,
        string $from,
        string $to
    ): string {
        return (string) StoreSales::query()
            ->where('gym_id', $gymId)
            ->whereBetween('sale_date', [$from, $to])
            ->sum('total_amount');
    }

    public function getTotalExpenses(
        int $gymId,
        string $from,
        string $to
    ): string {
        return (string) Expense::query()
            ->where('gym_id', $gymId)
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');
    }
}
