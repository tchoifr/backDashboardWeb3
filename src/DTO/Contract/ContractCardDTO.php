<?php

namespace App\DTO\Contract;

class ContractCardDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $companyName,
        public readonly string $amountUsdc,
        public readonly ?string $periodStart,
        public readonly ?string $periodEnd,
        public readonly ?string $status,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'companyName' => $this->companyName,
            'amountUsdc' => $this->amountUsdc,
            'periodStart' => $this->periodStart,
            'periodEnd' => $this->periodEnd,
            'status' => $this->status,
        ];
    }
}
