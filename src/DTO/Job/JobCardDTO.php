<?php

namespace App\DTO\Job;

class JobCardDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $companyName,
        public readonly ?string $location,
        public readonly string $datePosted,
        public readonly ?string $jobType,
        public readonly ?string $status,
        public readonly ?string $budgetMin,
        public readonly ?string $budgetMax,
        public readonly array $tags,
        public readonly int $applicationsCount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'companyName' => $this->companyName,
            'location' => $this->location,
            'datePosted' => $this->datePosted,
            'jobType' => $this->jobType,
            'status' => $this->status,
            'budgetMin' => $this->budgetMin,
            'budgetMax' => $this->budgetMax,
            'tags' => $this->tags,
            'applicationsCount' => $this->applicationsCount,
        ];
    }
}
