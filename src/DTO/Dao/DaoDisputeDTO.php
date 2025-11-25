<?php

namespace App\DTO\Dao;

class DaoDisputeDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $contractTitle,
        public readonly ?string $companyName,
        public readonly ?string $expectedSummary,
        public readonly ?string $deliveredSummary,
        public readonly ?string $amountUsdc,
        public readonly ?string $periodStart,
        public readonly ?string $periodEnd,
        public readonly int $votesFavorable,
        public readonly int $votesTotal,
        public readonly ?string $status,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'contractTitle' => $this->contractTitle,
            'companyName' => $this->companyName,
            'expectedSummary' => $this->expectedSummary,
            'deliveredSummary' => $this->deliveredSummary,
            'amountUsdc' => $this->amountUsdc,
            'periodStart' => $this->periodStart,
            'periodEnd' => $this->periodEnd,
            'votesFavorable' => $this->votesFavorable,
            'votesTotal' => $this->votesTotal,
            'status' => $this->status,
        ];
    }
}
