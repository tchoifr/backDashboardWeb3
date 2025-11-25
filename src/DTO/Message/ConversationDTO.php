<?php

namespace App\DTO\Message;

use App\DTO\Profile\UserProfileDTO;

class ConversationDTO
{
    /**
     * @param MessageDTO[] $messages
     */
    public function __construct(
        public readonly int $conversationId,
        public readonly UserProfileDTO $counterpart,
        public readonly array $messages,
    ) {
    }

    public function toArray(): array
    {
        return [
            'conversationId' => $this->conversationId,
            'counterpart' => $this->counterpart->toArray(),
            'messages' => array_map(static fn (MessageDTO $dto) => $dto->toArray(), $this->messages),
        ];
    }
}
