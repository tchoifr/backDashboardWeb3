<?php

namespace App\DTO\Message;

class MessageDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $senderId,
        public readonly string $content,
        public readonly string $sentAt,
        public readonly bool $isMine,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'senderId' => $this->senderId,
            'content' => $this->content,
            'sentAt' => $this->sentAt,
            'isMine' => $this->isMine,
        ];
    }
}
