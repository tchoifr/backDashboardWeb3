<?php

namespace App\Controller\Api;

use App\Service\Message\MessageService;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends BaseApiController
{
    public function __construct(private readonly MessageService $messageService)
    {
    }

    public function conversations(): JsonResponse
    {
        $conversations = $this->messageService->listConversations($this->user());

        return $this->jsonResponse([
            'conversations' => array_map(fn ($dto) => $dto->toArray(), $conversations),
        ]);
    }

    public function conversation(int $id): JsonResponse
    {
        try {
            $conversation = $this->messageService->getConversation($this->user(), $id);
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage(), JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($conversation->toArray());
    }

    public function create(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);
        try {
            $message = $this->messageService->sendMessage(
                $this->user(),
                (int) ($payload['counterpartId'] ?? 0),
                (string) ($payload['content'] ?? ''),
                $payload['jobId'] ?? null,
                $payload['contractId'] ?? null,
            );
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->jsonResponse($message->toArray(), JsonResponse::HTTP_CREATED);
    }

    public function send(int $id, Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);
        try {
            $message = $this->messageService->sendMessage(
                $this->user(),
                $id,
                (string) ($payload['content'] ?? ''),
                $payload['jobId'] ?? null,
                $payload['contractId'] ?? null,
            );
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->jsonResponse($message->toArray(), JsonResponse::HTTP_CREATED);
    }
}
