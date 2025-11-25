<?php

namespace App\DTO\Dao;

class DaoVoteDTO
{
    public function __construct(
        public readonly int $memberId,
        public readonly int $disputeId,
        public readonly string $vote,
    ) {
    }

    public function toArray(): array
    {
        return [
            'memberId' => $this->memberId,
            'disputeId' => $this->disputeId,
            'vote' => $this->vote,
        ];
    }
}
