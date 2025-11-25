<?php

namespace App\Service\Contract;

use App\DTO\Contract\ContractCardDTO;
use App\DTO\Contract\ContractDetailDTO;
use App\Entity\Contract;
use App\Entity\User;
use App\Mapper\ContractMapper;
use App\Repository\ContractRepository;
use App\Repository\JobRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class ContractService
{
    public function __construct(
        private readonly ContractRepository $contractRepository,
        private readonly UserRepository $userRepository,
        private readonly JobRepository $jobRepository,
        private readonly ContractMapper $contractMapper,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return ContractCardDTO[]
     */
    public function listContracts(User $user): array
    {
        $contracts = $this->contractRepository->findForUser($user);

        return array_map(fn (Contract $contract) => $this->contractMapper->toCardDTO($contract), $contracts);
    }

    public function getContractDetail(int $id): ContractDetailDTO
    {
        $contract = $this->contractRepository->find($id);
        if (!$contract) {
            throw new InvalidArgumentException('Contract not found');
        }

        return $this->contractMapper->toDetailDTO($contract);
    }

    public function createContract(User $client, array $payload): ContractCardDTO
    {
        $freelancerId = $payload['freelancerId'] ?? null;
        if (!$freelancerId) {
            throw new InvalidArgumentException('Freelancer is required.');
        }

        $freelancer = $this->userRepository->find($freelancerId);
        if (!$freelancer) {
            throw new InvalidArgumentException('Freelancer not found.');
        }

        $contract = new Contract();
        $contract->setClient($client)
            ->setFreelancer($freelancer)
            ->setTitle($payload['title'] ?? 'Contract')
            ->setCompanyName($payload['companyName'] ?? null)
            ->setAmountUsdc($payload['amountUsdc'] ?? '0')
            ->setStatus($payload['status'] ?? 'pending')
            ->setScopeExpected($payload['scopeExpected'] ?? null)
            ->setScopeDelivered($payload['scopeDelivered'] ?? null);

        if (isset($payload['jobId'])) {
            $job = $this->jobRepository->find($payload['jobId']);
            if ($job) {
                $contract->setJob($job);
            }
        }

        if (isset($payload['periodStart'])) {
            $contract->setPeriodStart(new DateTimeImmutable($payload['periodStart']));
        }
        if (isset($payload['periodEnd'])) {
            $contract->setPeriodEnd(new DateTimeImmutable($payload['periodEnd']));
        }

        $this->entityManager->persist($contract);
        $this->entityManager->flush();

        return $this->contractMapper->toCardDTO($contract);
    }

    public function updateContract(Contract $contract, array $payload): ContractCardDTO
    {
        if (isset($payload['status'])) {
            $contract->setStatus($payload['status']);
        }
        if (isset($payload['scopeDelivered'])) {
            $contract->setScopeDelivered($payload['scopeDelivered']);
        }
        if (isset($payload['amountUsdc'])) {
            $contract->setAmountUsdc($payload['amountUsdc']);
        }

        $this->entityManager->flush();

        return $this->contractMapper->toCardDTO($contract);
    }
}
