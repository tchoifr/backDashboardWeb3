<?php

namespace App\DTO\Dashboard;

class DashboardOverviewDTO
{
    /**
     * @param ProjectCardDTO[] $activeProjects
     * @param TransactionItemDTO[] $recentTransactions
     */
    public function __construct(
        public readonly DashboardStatsDTO $stats,
        public readonly array $activeProjects,
        public readonly array $recentTransactions,
    ) {
    }

    public function toArray(): array
    {
        return [
            'stats' => $this->stats->toArray(),
            'activeProjects' => array_map(static fn (ProjectCardDTO $dto) => $dto->toArray(), $this->activeProjects),
            'recentTransactions' => array_map(static fn (TransactionItemDTO $dto) => $dto->toArray(), $this->recentTransactions),
        ];
    }
}
