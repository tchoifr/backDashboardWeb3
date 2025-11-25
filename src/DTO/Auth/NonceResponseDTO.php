<?php

namespace App\DTO\Auth;

class NonceResponseDTO
{
    public function __construct(
        public readonly string $message,
    ) {
    }

    public function toArray(): array
    {
        return ['message' => $this->message];
    }
}
