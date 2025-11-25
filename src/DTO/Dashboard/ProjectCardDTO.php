<?php

namespace App\DTO\Dashboard;

class ProjectCardDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $companyName,
        public readonly ?string $amountUsdc,
        public readonly ?string $status,
        public readonly ?string $deadline,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'companyName' => $this->companyName,
            'amountUsdc' => $this->amountUsdc,
            'status' => $this->status,
            'deadline' => $this->deadline,
        ];
    }
}
