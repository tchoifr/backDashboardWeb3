<?php

namespace App\Mapper;

use App\DTO\Contract\ContractCardDTO;
use App\DTO\Contract\ContractDetailDTO;
use App\DTO\Dashboard\ProjectCardDTO;
use App\DTO\Dashboard\TransactionItemDTO;
use App\DTO\Dao\DaoDisputeDTO;
use App\Entity\Contract;
use DateTimeInterface;

class ContractMapper
{
    public function __construct(
        private readonly ProjectMapper $projectMapper,
        private readonly DaoMapper $daoMapper,
        private readonly TransactionMapper $transactionMapper,
    ) {
    }

    public function toCardDTO(Contract $contract): ContractCardDTO
    {
        return new ContractCardDTO(
            id: $contract->getId() ?? 0,
            title: $contract->getTitle(),
            companyName: $contract->getCompanyName(),
            amountUsdc: $contract->getAmountUsdc(),
            periodStart: $this->formatDate($contract->getPeriodStart()),
            periodEnd: $this->formatDate($contract->getPeriodEnd()),
            status: $contract->getStatus(),
        );
    }

    public function toDetailDTO(Contract $contract): ContractDetailDTO
    {
        $project = $contract->getProject();
        $disputes = array_map(
            fn ($dispute): DaoDisputeDTO => $this->daoMapper->toDisputeDTO($dispute),
            $contract->getDaoDisputes()->toArray()
        );
        $transactions = array_map(
            fn ($transaction): TransactionItemDTO => $this->transactionMapper->toItemDTO($transaction),
            $contract->getTransactions()->toArray()
        );

        return new ContractDetailDTO(
            contract: $this->toCardDTO($contract),
            project: $project ? $this->projectMapper->toCardDTO($project) : null,
            disputes: $disputes,
            transactions: $transactions,
            scopeExpected: $contract->getScopeExpected(),
            scopeDelivered: $contract->getScopeDelivered(),
        );
    }

    private function formatDate(?DateTimeInterface $date): ?string
    {
        return $date?->format(DateTimeInterface::ATOM);
    }
}
