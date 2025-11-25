<?php

namespace App\Controller\Api;

use App\Service\Profile\ProfileService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends BaseApiController
{
    public function __construct(private readonly ProfileService $profileService)
    {
    }

    public function me(): JsonResponse
    {
        $dto = $this->profileService->getProfile($this->user());

        return $this->jsonResponse($dto->toArray());
    }

    public function update(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);
        $dto = $this->profileService->updateProfile($this->user(), $payload);

        return $this->jsonResponse($dto->toArray());
    }
}
