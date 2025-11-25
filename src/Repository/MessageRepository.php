<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return array<int, array{counterpart: User, lastMessage: Message}>
     */
    public function findConversations(User $user): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.sender = :user OR m.receiver = :user')
            ->setParameter('user', $user)
            ->orderBy('m.sentAt', 'DESC');

        $conversations = [];

        foreach ($qb->getQuery()->getResult() as $message) {
            \assert($message instanceof Message);
            $counterpart = $message->getSender() === $user ? $message->getReceiver() : $message->getSender();
            if (!$counterpart) {
                continue;
            }

            $key = $counterpart->getId();
            if (!isset($conversations[$key])) {
                $conversations[$key] = [
                    'counterpart' => $counterpart,
                    'lastMessage' => $message,
                ];
            }
        }

        return array_values($conversations);
    }

    /**
     * @return Message[]
     */
    public function findMessagesBetween(User $user, User $counterpart): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user AND m.receiver = :counterpart) OR (m.sender = :counterpart AND m.receiver = :user)')
            ->setParameter('user', $user)
            ->setParameter('counterpart', $counterpart)
            ->orderBy('m.sentAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
