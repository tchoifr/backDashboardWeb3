<?php

namespace App\Controller\Api;

use App\Service\Auth\AuthService;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends BaseApiController
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function nonce(Request $request): JsonResponse
    {
        $address = (string) $request->query->get('address', '');
        try {
            $dto = $this->authService->generateNonce($address);
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->jsonResponse($dto->toArray());
    }

    public function verify(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);
        try {
            $dto = $this->authService->verifySignature(
                (string) ($payload['address'] ?? ''),
                (string) ($payload['signature'] ?? '')
            );
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->jsonResponse($dto->toArray());
    }
}
