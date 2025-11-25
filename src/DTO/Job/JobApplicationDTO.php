<?php

namespace App\DTO\Job;

class JobApplicationDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $candidateId,
        public readonly string $candidateName,
        public readonly ?string $candidateAvatar,
        public readonly string $status,
        public readonly ?string $message,
        public readonly string $createdAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'candidateId' => $this->candidateId,
            'candidateName' => $this->candidateName,
            'candidateAvatar' => $this->candidateAvatar,
            'status' => $this->status,
            'message' => $this->message,
            'createdAt' => $this->createdAt,
        ];
    }
}
