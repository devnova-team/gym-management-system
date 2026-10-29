<?php

namespace App\Services;
use App\Repositories\FinancialReportRepository;
use Carbon\Carbon;

class FinancialReportService
{
    
    public function __construct(
        protected FinancialReportRepository $financialReportRepository
    ) { }

     public function getFinancialSummary(
        int $gymId,
        string $from,
        string $to
    ): array {

        // Convert Cairo date boundaries to UTC for subscription payments.
        $fromUtc = Carbon::createFromFormat(
            '!Y-m-d',
            $from,
            'Africa/Cairo'
        )
            ->startOfDay()
            ->utc()
            ->format('Y-m-d H:i:s');

        $toUtc = Carbon::createFromFormat(
            '!Y-m-d',
            $to,
            'Africa/Cairo'
        )
            ->endOfDay()
            ->utc()
            ->format('Y-m-d H:i:s');

        // Get revenues and expenses from the repository.
        $subscriptionRevenue = $this->financialReportRepository
            ->getSubscriptionRevenue($gymId, $fromUtc, $toUtc);

        $storeRevenue = $this->financialReportRepository
            ->getStoreRevenue($gymId, $from, $to);

        $totalExpenses = $this->financialReportRepository
            ->getTotalExpenses($gymId, $from, $to);

        // Calculate total revenue.
        $totalRevenue = bcadd(
            $subscriptionRevenue,
            $storeRevenue,
            2
        );

        // Calculate net profit.
        $netProfit = bcsub(
            $totalRevenue,
            $totalExpenses,
            2
        );

        return [
            'from' => $from,
            'to' => $to,
            'subscription_revenue' => bcadd($subscriptionRevenue, '0', 2),
            'store_revenue' => bcadd($storeRevenue, '0', 2),
            'total_revenue' => $totalRevenue,
            'total_expenses' => bcadd($totalExpenses, '0', 2),
            'net_profit' => $netProfit,
        ];
    }
}
