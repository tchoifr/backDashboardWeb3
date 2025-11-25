<?php

namespace App\DTO\Contract;

use App\DTO\Dashboard\ProjectCardDTO;
use App\DTO\Dashboard\TransactionItemDTO;
use App\DTO\Dao\DaoDisputeDTO;

class ContractDetailDTO
{
    /**
     * @param DaoDisputeDTO[] $disputes
     * @param TransactionItemDTO[] $transactions
     */
    public function __construct(
        public readonly ContractCardDTO $contract,
        public readonly ?ProjectCardDTO $project,
        public readonly array $disputes,
        public readonly array $transactions,
        public readonly ?string $scopeExpected,
        public readonly ?string $scopeDelivered,
    ) {
    }

    public function toArray(): array
    {
        return [
            'contract' => $this->contract->toArray(),
            'project' => $this->project?->toArray(),
            'disputes' => array_map(static fn (DaoDisputeDTO $dto) => $dto->toArray(), $this->disputes),
            'transactions' => array_map(static fn (TransactionItemDTO $dto) => $dto->toArray(), $this->transactions),
            'scopeExpected' => $this->scopeExpected,
            'scopeDelivered' => $this->scopeDelivered,
        ];
    }
}
