<?php

namespace App\Controller\Api;

use App\DTO\Job\JobApplicationDTO;
use App\Repository\JobRepository;
use App\Service\Job\JobService;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JobController extends BaseApiController
{
    public function __construct(
        private readonly JobService $jobService,
        private readonly JobRepository $jobRepository,
    ) {
    }

    public function list(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->query->get('search'),
            'location' => $request->query->get('location'),
            'tags' => $request->query->all('tags'),
            'type' => $request->query->get('type'),
        ];
        $jobs = $this->jobService->listJobs($filters);

        return $this->jsonResponse(['jobs' => array_map(fn ($dto) => $dto->toArray(), $jobs)]);
    }

    public function myJobs(): JsonResponse
    {
        $jobs = $this->jobService->listMyJobs($this->user());

        return $this->jsonResponse(['jobs' => array_map(fn ($dto) => $dto->toArray(), $jobs)]);
    }

    public function detail(int $id): JsonResponse
    {
        try {
            $dto = $this->jobService->getJobDetail($id);
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage(), JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($dto->toArray());
    }

    public function create(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);
        $dto = $this->jobService->createJob($this->user(), $payload);

        return $this->jsonResponse($dto->toArray(), JsonResponse::HTTP_CREATED);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $job = $this->jobRepository->find($id);
        if (!$job) {
            return $this->errorResponse('Job not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $payload = $this->parseJson($request);
        $dto = $this->jobService->updateJob($job, $payload);

        return $this->jsonResponse($dto->toArray());
    }

    public function apply(int $id, Request $request): JsonResponse
    {
        $job = $this->jobRepository->find($id);
        if (!$job) {
            return $this->errorResponse('Job not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $payload = $this->parseJson($request);
        $dto = $this->jobService->apply($this->user(), $job, $payload['message'] ?? null);

        return $this->jsonResponse($dto->toArray(), JsonResponse::HTTP_CREATED);
    }

    public function applications(int $id): JsonResponse
    {
        $job = $this->jobRepository->find($id);
        if (!$job) {
            return $this->errorResponse('Job not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $applications = $this->jobService->listApplications($job);

        return $this->jsonResponse([
            'applications' => array_map(fn (JobApplicationDTO $dto) => $dto->toArray(), $applications),
        ]);
    }
}
