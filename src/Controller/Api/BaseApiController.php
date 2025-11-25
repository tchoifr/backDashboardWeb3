<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseApiController extends AbstractController
{
    protected function user(): User
    {
        $user = $this->getUser();
        \assert($user instanceof User);

        return $user;
    }

    protected function jsonResponse(array $payload, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return new JsonResponse($payload, $status);
    }

    protected function errorResponse(string $message, int $status = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse(['error' => $message], $status);
    }

    protected function parseJson(Request $request): array
    {
        $data = json_decode((string) $request->getContent(), true) ?? [];
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }
}
