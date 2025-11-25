<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\JobApplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobApplication>
 */
class JobApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobApplication::class);
    }

    public function countForJob(Job $job): int
    {
        return (int) $this->createQueryBuilder('ja')
            ->select('COUNT(ja.id)')
            ->where('ja.job = :job')
            ->setParameter('job', $job)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
