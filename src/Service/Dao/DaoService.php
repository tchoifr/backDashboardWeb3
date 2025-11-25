<?php

namespace App\Service\Dao;

use App\DTO\Dao\DaoDisputeDTO;
use App\DTO\Dao\DaoVoteDTO;
use App\Entity\DaoDispute;
use App\Entity\User;
use App\Mapper\DaoMapper;
use App\Repository\ContractRepository;
use App\Repository\DaoDisputeRepository;
use App\Repository\DaoMemberRepository;
use App\Repository\DaoRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class DaoService
{
    public function __construct(
        private readonly DaoDisputeRepository $daoDisputeRepository,
        private readonly DaoRepository $daoRepository,
        private readonly DaoMemberRepository $daoMemberRepository,
        private readonly ContractRepository $contractRepository,
        private readonly DaoMapper $daoMapper,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return DaoDisputeDTO[]
     */
    public function listDisputes(): array
    {
        return array_map(
            fn (DaoDispute $dispute) => $this->daoMapper->toDisputeDTO($dispute),
            $this->daoDisputeRepository->findOpen()
        );
    }

    public function getDispute(int $id): DaoDisputeDTO
    {
        $dispute = $this->daoDisputeRepository->find($id);
        if (!$dispute) {
            throw new InvalidArgumentException('Dispute not found.');
        }

        return $this->daoMapper->toDisputeDTO($dispute);
    }

    public function createDispute(User $user, array $payload): DaoDisputeDTO
    {
        $dao = $this->daoRepository->find($payload['daoId'] ?? 0);
        $contract = $this->contractRepository->find($payload['contractId'] ?? 0);
        if (!$dao || !$contract) {
            throw new InvalidArgumentException('DAO or contract missing.');
        }

        $dispute = new DaoDispute();
        $dispute->setDao($dao)
            ->setContract($contract)
            ->setRaisedBy($user)
            ->setDescriptionExpected($payload['descriptionExpected'] ?? null)
            ->setDescriptionDelivered($payload['descriptionDelivered'] ?? null)
            ->setAmountUsdc($payload['amountUsdc'] ?? null)
            ->setStatus('open');

        if (isset($payload['periodStart'])) {
            $dispute->setPeriodStart(new DateTimeImmutable($payload['periodStart']));
        }
        if (isset($payload['periodEnd'])) {
            $dispute->setPeriodEnd(new DateTimeImmutable($payload['periodEnd']));
        }

        $this->entityManager->persist($dispute);
        $this->entityManager->flush();

        return $this->daoMapper->toDisputeDTO($dispute);
    }

    public function vote(User $user, int $disputeId, string $vote): DaoVoteDTO
    {
        $dispute = $this->daoDisputeRepository->find($disputeId);
        if (!$dispute) {
            throw new InvalidArgumentException('Dispute not found.');
        }

        $membership = $this->daoMemberRepository->findOneBy([
            'dao' => $dispute->getDao(),
            'user' => $user,
        ]);

        if (!$membership) {
            throw new InvalidArgumentException('User not allowed to vote on this DAO.');
        }

        $dispute->setVotesTotal($dispute->getVotesTotal() + 1);
        if ($vote === 'yes') {
            $dispute->setVotesFavorable($dispute->getVotesFavorable() + 1);
        }

        $this->entityManager->flush();

        return new DaoVoteDTO($membership->getId() ?? 0, $dispute->getId() ?? 0, $vote);
    }
}
