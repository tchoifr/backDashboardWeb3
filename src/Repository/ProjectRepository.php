<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @return Project[]
     */
    public function findActiveByUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.contract', 'c')->addSelect('c')
            ->where('p.freelancer = :user OR p.client = :user')
            ->andWhere('p.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('statuses', ['active', 'pending'])
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
