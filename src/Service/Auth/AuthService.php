<?php

namespace App\Service\Auth;

use App\DTO\Auth\AuthTokenDTO;
use App\DTO\Auth\NonceResponseDTO;
use App\Entity\User;
use App\Mapper\UserMapper;
use App\Repository\UserRepository;
use App\Security\JwtGenerator;
use App\Security\WalletSignatureValidator;
use DateTimeImmutable;
use InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;

class AuthService
{
    private const CACHE_PREFIX = 'auth_nonce_';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CacheItemPoolInterface $cache,
        private readonly WalletSignatureValidator $walletSignatureValidator,
        private readonly JwtGenerator $jwtGenerator,
        private readonly UserMapper $userMapper,
    ) {
    }

    public function generateNonce(string $walletAddress): NonceResponseDTO
    {
        $walletAddress = $this->normalizeWalletAddress($walletAddress);
        $nonce = bin2hex(random_bytes(16));
        $message = sprintf('Sign this nonce %s to securely log in to ProjectWeb3.', $nonce);

        $item = $this->cache->getItem($this->cacheKey($walletAddress));
        $item->set(['message' => $message]);
        $item->expiresAfter(300);
        $this->cache->save($item);

        return new NonceResponseDTO($message);
    }

    public function verifySignature(string $walletAddress, string $signature): AuthTokenDTO
    {
        $walletAddress = $this->normalizeWalletAddress($walletAddress);
        $item = $this->cache->getItem($this->cacheKey($walletAddress));
        if (!$item->isHit()) {
            throw new InvalidArgumentException('Nonce expired or invalid.');
        }

        $message = $item->get()['message'] ?? null;
        if (!$message) {
            throw new InvalidArgumentException('Nonce payload corrupted.');
        }

        if (!$this->walletSignatureValidator->isValidSignature($walletAddress, $message, $signature)) {
            throw new InvalidArgumentException('Signature mismatch.');
        }

        $this->cache->deleteItem($this->cacheKey($walletAddress));

        $user = $this->userRepository->findOneBy(['walletAddress' => $walletAddress]);
        if (!$user) {
            $user = (new User())
                ->setWalletAddress($walletAddress)
                ->setUsername(substr($walletAddress, 0, 6) . '...' . substr($walletAddress, -4));
            $this->userRepository->save($user);
        }

        $user->setLastLoginAt(new DateTimeImmutable());
        $this->userRepository->save($user, true);

        $token = $this->jwtGenerator->createToken($user);

        return new AuthTokenDTO(
            accessToken: $token['token'],
            expiresIn: $token['expiresIn'],
            user: $this->userMapper->toProfileDTO($user),
        );
    }

    private function cacheKey(string $walletAddress): string
    {
        return self::CACHE_PREFIX . $walletAddress;
    }

    private function normalizeWalletAddress(string $walletAddress): string
    {
        $walletAddress = strtolower(trim($walletAddress));
        if (!preg_match('/^0x[a-f0-9]{40}$/', $walletAddress)) {
            throw new InvalidArgumentException('Invalid wallet address.');
        }

        return $walletAddress;
    }
}
