<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @return Job[]
     */
    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('j')
            ->leftJoin('j.postedBy', 'postedBy')
            ->addSelect('postedBy');

        if ($search = $criteria['search'] ?? null) {
            $qb->andWhere('LOWER(j.title) LIKE :search OR LOWER(j.description) LIKE :search')
                ->setParameter('search', '%' . strtolower($search) . '%');
        }

        if ($location = $criteria['location'] ?? null) {
            $qb->andWhere('LOWER(j.location) LIKE :location')
                ->setParameter('location', '%' . strtolower($location) . '%');
        }

        if ($type = $criteria['type'] ?? null) {
            $qb->andWhere('j.jobType = :type')->setParameter('type', $type);
        }

        $results = $qb
            ->orderBy('j.createdAt', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();

        if ($tags = $criteria['tags'] ?? null) {
            $results = array_values(array_filter($results, static function (Job $job) use ($tags): bool {
                $jobTags = $job->getTags() ?? [];

                return count(array_intersect($tags, $jobTags)) === count($tags);
            }));
        }

        return $results;
    }

    /**
     * @return Job[]
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('j')
            ->where('j.postedBy = :user OR j.assignedTo = :user')
            ->setParameter('user', $user)
            ->orderBy('j.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
