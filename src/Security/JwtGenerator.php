<?php

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class JwtGenerator
{
    public function __construct(
        private readonly JWTTokenManagerInterface $tokenManager,
        private readonly int $tokenTtl,
    ) {
    }

    public function createToken(User $user): array
    {
        $token = $this->tokenManager->create($user);

        return [
            'token' => $token,
            'expiresIn' => $this->tokenTtl,
        ];
    }
}
