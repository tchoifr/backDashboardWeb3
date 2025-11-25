<?php

namespace App\Mapper;

use App\DTO\Dao\DaoDisputeDTO;
use App\Entity\DaoDispute;
use DateTimeInterface;

class DaoMapper
{
    public function toDisputeDTO(DaoDispute $dispute): DaoDisputeDTO
    {
        $contract = $dispute->getContract();

        return new DaoDisputeDTO(
            id: $dispute->getId() ?? 0,
            contractTitle: $contract?->getTitle() ?? 'Unknown contract',
            companyName: $contract?->getCompanyName(),
            expectedSummary: $dispute->getDescriptionExpected(),
            deliveredSummary: $dispute->getDescriptionDelivered(),
            amountUsdc: $dispute->getAmountUsdc(),
            periodStart: $this->formatDate($dispute->getPeriodStart()),
            periodEnd: $this->formatDate($dispute->getPeriodEnd()),
            votesFavorable: $dispute->getVotesFavorable(),
            votesTotal: $dispute->getVotesTotal(),
            status: $dispute->getStatus(),
        );
    }

    private function formatDate(?DateTimeInterface $date): ?string
    {
        return $date?->format(DateTimeInterface::ATOM);
    }
}
