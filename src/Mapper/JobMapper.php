<?php

namespace App\Mapper;

use App\DTO\Job\JobApplicationDTO;
use App\DTO\Job\JobCardDTO;
use App\Entity\Job;
use App\Entity\JobApplication;
use App\Repository\JobApplicationRepository;
use DateTimeInterface;

class JobMapper
{
    public function __construct(private readonly JobApplicationRepository $jobApplicationRepository)
    {
    }

    public function toCardDTO(Job $job): JobCardDTO
    {
        $applicationsCount = $this->jobApplicationRepository->countForJob($job);

        return new JobCardDTO(
            id: $job->getId() ?? 0,
            title: $job->getTitle(),
            companyName: $job->getCompanyName(),
            location: $job->getLocation(),
            datePosted: $job->getDatePosted()->format(DateTimeInterface::ATOM),
            jobType: $job->getJobType(),
            status: $job->getStatus(),
            budgetMin: $job->getBudgetMin(),
            budgetMax: $job->getBudgetMax(),
            tags: $job->getTags() ?? [],
            applicationsCount: $applicationsCount,
        );
    }

    public function toApplicationDTO(JobApplication $application): JobApplicationDTO
    {
        $candidate = $application->getCandidate();

        return new JobApplicationDTO(
            id: $application->getId() ?? 0,
            candidateId: $candidate?->getId() ?? 0,
            candidateName: $candidate?->getUsername() ?? $candidate?->getWalletAddress() ?? 'unknown',
            candidateAvatar: $candidate?->getAvatarUrl(),
            status: $application->getStatus(),
            message: $application->getMessage(),
            createdAt: $application->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }
}
