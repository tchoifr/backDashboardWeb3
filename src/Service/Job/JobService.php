<?php

namespace App\Service\Job;

use App\DTO\Job\JobApplicationDTO;
use App\DTO\Job\JobCardDTO;
use App\DTO\Job\JobDetailDTO;
use App\Entity\Job;
use App\Entity\JobApplication;
use App\Entity\User;
use App\Mapper\JobMapper;
use App\Mapper\UserMapper;
use App\Repository\JobApplicationRepository;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class JobService
{
    public function __construct(
        private readonly JobRepository $jobRepository,
        private readonly JobApplicationRepository $jobApplicationRepository,
        private readonly JobMapper $jobMapper,
        private readonly UserMapper $userMapper,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return JobCardDTO[]
     */
    public function listJobs(array $filters = []): array
    {
        $jobs = $this->jobRepository->search($filters);

        return array_map(fn (Job $job) => $this->jobMapper->toCardDTO($job), $jobs);
    }

    /**
     * @return JobCardDTO[]
     */
    public function listMyJobs(User $user): array
    {
        $jobs = $this->jobRepository->findForUser($user);

        return array_map(fn (Job $job) => $this->jobMapper->toCardDTO($job), $jobs);
    }

    public function getJobDetail(int $id): JobDetailDTO
    {
        $job = $this->jobRepository->find($id);
        if (!$job) {
            throw new InvalidArgumentException('Job not found');
        }

        $applications = $this->jobApplicationRepository->findBy(['job' => $job]);
        $applicationDTOs = array_map(fn (JobApplication $application) => $this->jobMapper->toApplicationDTO($application), $applications);

        return new JobDetailDTO(
            job: $this->jobMapper->toCardDTO($job),
            postedBy: $job->getPostedBy() ? $this->userMapper->toProfileDTO($job->getPostedBy()) : null,
            assignedTo: $job->getAssignedTo() ? $this->userMapper->toProfileDTO($job->getAssignedTo()) : null,
            description: $job->getDescription(),
            applications: $applicationDTOs,
        );
    }

    public function createJob(User $postedBy, array $payload): JobCardDTO
    {
        $job = new Job();
        $job->setPostedBy($postedBy)
            ->setTitle($payload['title'] ?? 'Untitled role')
            ->setCompanyName($payload['companyName'] ?? null)
            ->setLocation($payload['location'] ?? null)
            ->setJobType($payload['jobType'] ?? null)
            ->setStatus($payload['status'] ?? 'pending')
            ->setBudgetMin($payload['budgetMin'] ?? null)
            ->setBudgetMax($payload['budgetMax'] ?? null)
            ->setTags($payload['tags'] ?? [])
            ->setDescription($payload['description'] ?? null);

        $this->entityManager->persist($job);
        $this->entityManager->flush();

        return $this->jobMapper->toCardDTO($job);
    }

    public function updateJob(Job $job, array $payload): JobCardDTO
    {
        if (isset($payload['title'])) {
            $job->setTitle($payload['title']);
        }
        if (array_key_exists('status', $payload)) {
            $job->setStatus($payload['status']);
        }
        if (array_key_exists('jobType', $payload)) {
            $job->setJobType($payload['jobType']);
        }
        if (array_key_exists('location', $payload)) {
            $job->setLocation($payload['location']);
        }
        if (array_key_exists('budgetMin', $payload)) {
            $job->setBudgetMin($payload['budgetMin']);
        }
        if (array_key_exists('budgetMax', $payload)) {
            $job->setBudgetMax($payload['budgetMax']);
        }
        if (array_key_exists('tags', $payload)) {
            $job->setTags($payload['tags']);
        }
        if (array_key_exists('description', $payload)) {
            $job->setDescription($payload['description']);
        }

        $this->entityManager->flush();

        return $this->jobMapper->toCardDTO($job);
    }

    public function apply(User $candidate, Job $job, ?string $message = null): JobApplicationDTO
    {
        $existing = $this->jobApplicationRepository->findOneBy([
            'job' => $job,
            'candidate' => $candidate,
        ]);

        if ($existing) {
            return $this->jobMapper->toApplicationDTO($existing);
        }

        $application = new JobApplication();
        $application->setJob($job)
            ->setCandidate($candidate)
            ->setMessage($message)
            ->setStatus('pending');

        $this->entityManager->persist($application);
        $this->entityManager->flush();

        return $this->jobMapper->toApplicationDTO($application);
    }

    /**
     * @return JobApplicationDTO[]
     */
    public function listApplications(Job $job): array
    {
        $applications = $this->jobApplicationRepository->findBy(['job' => $job]);

        return array_map(fn (JobApplication $application) => $this->jobMapper->toApplicationDTO($application), $applications);
    }
}
