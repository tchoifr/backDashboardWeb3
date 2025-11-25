<?php

namespace App\DTO\Dashboard;

class TransactionItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $label,
        public readonly string $amountUsdc,
        public readonly string $direction,
        public readonly string $date,
        public readonly ?string $status,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'amountUsdc' => $this->amountUsdc,
            'direction' => $this->direction,
            'date' => $this->date,
            'status' => $this->status,
        ];
    }
}
