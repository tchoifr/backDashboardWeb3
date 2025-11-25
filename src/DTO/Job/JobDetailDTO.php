<?php

namespace App\DTO\Job;

use App\DTO\Profile\UserProfileDTO;

class JobDetailDTO
{
    /**
     * @param JobApplicationDTO[] $applications
     */
    public function __construct(
        public readonly JobCardDTO $job,
        public readonly ?UserProfileDTO $postedBy,
        public readonly ?UserProfileDTO $assignedTo,
        public readonly ?string $description,
        public readonly array $applications,
    ) {
    }

    public function toArray(): array
    {
        return [
            'job' => $this->job->toArray(),
            'postedBy' => $this->postedBy?->toArray(),
            'assignedTo' => $this->assignedTo?->toArray(),
            'description' => $this->description,
            'applications' => array_map(static fn (JobApplicationDTO $dto) => $dto->toArray(), $this->applications),
        ];
    }
}
