<?php

namespace App\DTO\Auth;

use App\DTO\Profile\UserProfileDTO;

class AuthTokenDTO
{
    public function __construct(
        public readonly string $accessToken,
        public readonly int $expiresIn,
        public readonly UserProfileDTO $user,
    ) {
    }

    public function toArray(): array
    {
        return [
            'accessToken' => $this->accessToken,
            'expiresIn' => $this->expiresIn,
            'user' => $this->user->toArray(),
        ];
    }
}
