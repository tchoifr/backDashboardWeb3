<?php

namespace App\Service\Dashboard;

use App\DTO\Dashboard\DashboardOverviewDTO;
use App\DTO\Dashboard\DashboardStatsDTO;
use App\Mapper\ProjectMapper;
use App\Mapper\TransactionMapper;
use App\Repository\ProjectRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use App\Entity\User;
use DateInterval;
use DateTimeImmutable;

class DashboardService
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly TransactionRepository $transactionRepository,
        private readonly UserRepository $userRepository,
        private readonly ProjectMapper $projectMapper,
        private readonly TransactionMapper $transactionMapper,
    ) {
    }

    public function getOverview(User $user): DashboardOverviewDTO
    {
        $projects = $this->projectRepository->findActiveByUser($user);
        $transactions = $this->transactionRepository->findRecentByUser($user, 12);
        $stats = $this->userRepository->getDashboardStats($user);

        $activeProjectDTOs = array_map(
            fn ($project) => $this->projectMapper->toCardDTO($project),
            $projects
        );
        $transactionDTOs = array_map(
            fn ($transaction) => $this->transactionMapper->toItemDTO($transaction),
            $transactions
        );

        $now = new DateTimeImmutable();
        $thirtyDaysAgo = $now->sub(new DateInterval('P30D'));
        $newProjectsCount = 0;
        $monthlyRevenue = 0.0;
        $balance = 0.0;

        foreach ($transactions as $transaction) {
            $amount = (float) $transaction->getAmountUsdc();
            $sign = $transaction->getDirection() === 'in' ? 1 : -1;
            $balance += $sign * $amount;
            if ($transaction->getDate() >= $thirtyDaysAgo && $sign > 0) {
                $monthlyRevenue += $amount;
            }
        }

        foreach ($projects as $project) {
            if ($project->getCreatedAt() >= $thirtyDaysAgo) {
                $newProjectsCount++;
            }
        }

        $statsDTO = new DashboardStatsDTO(
            totalBalanceUsdc: $balance,
            balanceChangePercent: 0.0,
            activeProjectsCount: $stats['activeProjectsCount'] ?? 0,
            newProjectsCount: $newProjectsCount,
            totalClientsCount: $stats['totalClientsCount'] ?? 0,
            newClientsCount: $stats['newClientsCount'] ?? 0,
            monthlyRevenueUsdc: $monthlyRevenue,
            monthlyRevenueChangePercent: 0.0,
        );

        return new DashboardOverviewDTO($statsDTO, $activeProjectDTOs, $transactionDTOs);
    }
}
