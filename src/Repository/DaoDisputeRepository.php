<?php

namespace App\Repository;

use App\Entity\DaoDispute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DaoDispute>
 */
class DaoDisputeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DaoDispute::class);
    }

    /**
     * @return DaoDispute[]
     */
    public function findOpen(): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.contract', 'c')->addSelect('c')
            ->leftJoin('d.dao', 'dao')->addSelect('dao')
            ->andWhere('d.status != :closed OR d.status IS NULL')
            ->setParameter('closed', 'resolved')
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
