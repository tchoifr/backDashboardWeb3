<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getDashboardStats(User $user): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(DISTINCT c.id) AS activeContracts')
            ->addSelect('COUNT(DISTINCT p.id) AS activeProjects')
            ->addSelect('COUNT(DISTINCT t.id) AS transactions')
            ->leftJoin('u.freelanceContracts', 'c')
            ->leftJoin('c.project', 'p')
            ->leftJoin('u.transactions', 't')
            ->where('u = :user')
            ->setParameter('user', $user);

        $result = $qb->getQuery()->getSingleResult();

        return [
            'activeProjectsCount' => (int) ($result['activeProjects'] ?? 0),
            'totalClientsCount' => count($user->getClientContracts()),
            'transactionsCount' => (int) ($result['transactions'] ?? 0),
            'newClientsCount' => 0,
        ];
    }
}
