<?php

namespace App\DTO\Profile;

class UserProfileDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $walletAddress,
        public readonly ?string $username,
        public readonly ?string $avatarUrl,
        public readonly ?string $title,
        public readonly ?string $location,
        public readonly ?string $availability,
        public readonly ?string $rateHourUsdc,
        public readonly ?string $bio,
        public readonly array $skills,
        public readonly array $highlights,
        public readonly array $portfolio,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'walletAddress' => $this->walletAddress,
            'username' => $this->username,
            'avatarUrl' => $this->avatarUrl,
            'title' => $this->title,
            'location' => $this->location,
            'availability' => $this->availability,
            'rateHourUsdc' => $this->rateHourUsdc,
            'bio' => $this->bio,
            'skills' => $this->skills,
            'highlights' => $this->highlights,
            'portfolio' => $this->portfolio,
        ];
    }
}
