<?php

namespace App\Controller\Api;

use App\Service\Dao\DaoService;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DaoController extends BaseApiController
{
    public function __construct(private readonly DaoService $daoService)
    {
    }

    public function disputes(): JsonResponse
    {
        $disputes = $this->daoService->listDisputes();

        return $this->jsonResponse(['disputes' => array_map(fn ($dto) => $dto->toArray(), $disputes)]);
    }

    public function detail(int $id): JsonResponse
    {
        try {
            $dto = $this->daoService->getDispute($id);
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage(), JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($dto->toArray());
    }

    public function create(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);
        $dto = $this->daoService->createDispute($this->user(), $payload);

        return $this->jsonResponse($dto->toArray(), JsonResponse::HTTP_CREATED);
    }

    public function vote(int $id, Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);
        try {
            $dto = $this->daoService->vote($this->user(), $id, $payload['vote'] ?? 'no');
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->jsonResponse($dto->toArray());
    }
}
