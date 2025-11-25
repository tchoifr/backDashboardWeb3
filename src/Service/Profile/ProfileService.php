<?php

namespace App\Service\Profile;

use App\DTO\Profile\UserProfileDTO;
use App\Entity\User;
use App\Mapper\UserMapper;
use Doctrine\ORM\EntityManagerInterface;

class ProfileService
{
    public function __construct(
        private readonly UserMapper $userMapper,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getProfile(User $user): UserProfileDTO
    {
        return $this->userMapper->toProfileDTO($user);
    }

    public function updateProfile(User $user, array $payload): UserProfileDTO
    {
        $fields = ['username', 'avatarUrl', 'title', 'location', 'availability', 'rateHourUsdc', 'bio'];
        foreach ($fields as $field) {
            if (array_key_exists($field, $payload)) {
                $setter = 'set' . ucfirst($field);
                if (method_exists($user, $setter)) {
                    $user->$setter($payload[$field]);
                }
            }
        }

        if (array_key_exists('skills', $payload)) {
            $user->setSkills($payload['skills']);
        }
        if (array_key_exists('highlights', $payload)) {
            $user->setHighlights($payload['highlights']);
        }
        if (array_key_exists('portfolio', $payload)) {
            $user->setPortfolio($payload['portfolio']);
        }

        $this->entityManager->flush();

        return $this->userMapper->toProfileDTO($user);
    }
}
