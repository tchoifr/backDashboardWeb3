<?php

namespace App\DTO\Message;

class ConversationListItemDTO
{
    public function __construct(
        public readonly int $conversationId,
        public readonly string $counterpartName,
        public readonly ?string $lastMessagePreview,
        public readonly ?string $lastMessageAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'conversationId' => $this->conversationId,
            'counterpartName' => $this->counterpartName,
            'lastMessagePreview' => $this->lastMessagePreview,
            'lastMessageAt' => $this->lastMessageAt,
        ];
    }
}
