<?php

namespace App\Controller\Api;

use App\Repository\ContractRepository;
use App\Service\Contract\ContractService;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContractController extends BaseApiController
{
    public function __construct(
        private readonly ContractService $contractService,
        private readonly ContractRepository $contractRepository,
    ) {
    }

    public function index(): JsonResponse
    {
        $contracts = $this->contractService->listContracts($this->user());

        return $this->jsonResponse(['contracts' => array_map(fn ($dto) => $dto->toArray(), $contracts)]);
    }

    public function detail(int $id): JsonResponse
    {
        try {
            $dto = $this->contractService->getContractDetail($id);
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage(), JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($dto->toArray());
    }

    public function create(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);
        $dto = $this->contractService->createContract($this->user(), $payload);

        return $this->jsonResponse($dto->toArray(), JsonResponse::HTTP_CREATED);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $contract = $this->contractRepository->find($id);
        if (!$contract) {
            return $this->errorResponse('Contract not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $payload = $this->parseJson($request);
        $dto = $this->contractService->updateContract($contract, $payload);

        return $this->jsonResponse($dto->toArray());
    }
}
