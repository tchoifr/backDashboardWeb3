<?php

namespace App\Controller\Api;

use App\Service\Dashboard\DashboardService;
use Symfony\Component\HttpFoundation\JsonResponse;

class DashboardController extends BaseApiController
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function overview(): JsonResponse
    {
        $dto = $this->dashboardService->getOverview($this->user());

        return $this->jsonResponse($dto->toArray());
    }
}
