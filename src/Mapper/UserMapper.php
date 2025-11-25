<?php

namespace App\Mapper;

use App\DTO\Profile\UserProfileDTO;
use App\Entity\User;

class UserMapper
{
    public function toProfileDTO(User $user): UserProfileDTO
    {
        return new UserProfileDTO(
            id: $user->getId() ?? 0,
            walletAddress: $user->getWalletAddress(),
            username: $user->getUsername(),
            avatarUrl: $user->getAvatarUrl(),
            title: $user->getTitle(),
            location: $user->getLocation(),
            availability: $user->getAvailability(),
            rateHourUsdc: $user->getRateHourUsdc(),
            bio: $user->getBio(),
            skills: $user->getSkills() ?? [],
            highlights: $user->getHighlights() ?? [],
            portfolio: $user->getPortfolio() ?? [],
        );
    }
}
