<?php

namespace App\DTO\Dashboard;

class DashboardStatsDTO
{
    public function __construct(
        public readonly float $totalBalanceUsdc,
        public readonly float $balanceChangePercent,
        public readonly int $activeProjectsCount,
        public readonly int $newProjectsCount,
        public readonly int $totalClientsCount,
        public readonly int $newClientsCount,
        public readonly float $monthlyRevenueUsdc,
        public readonly float $monthlyRevenueChangePercent,
    ) {
    }

    public function toArray(): array
    {
        return [
            'totalBalanceUsdc' => $this->totalBalanceUsdc,
            'balanceChangePercent' => $this->balanceChangePercent,
            'activeProjectsCount' => $this->activeProjectsCount,
            'newProjectsCount' => $this->newProjectsCount,
            'totalClientsCount' => $this->totalClientsCount,
            'newClientsCount' => $this->newClientsCount,
            'monthlyRevenueUsdc' => $this->monthlyRevenueUsdc,
            'monthlyRevenueChangePercent' => $this->monthlyRevenueChangePercent,
        ];
    }
}
