<?php

namespace App\Service\Message;

use App\DTO\Message\ConversationDTO;
use App\DTO\Message\ConversationListItemDTO;
use App\DTO\Message\MessageDTO;
use App\Entity\Message;
use App\Entity\User;
use App\Mapper\MessageMapper;
use App\Repository\ContractRepository;
use App\Repository\JobRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class MessageService
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly UserRepository $userRepository,
        private readonly JobRepository $jobRepository,
        private readonly ContractRepository $contractRepository,
        private readonly MessageMapper $messageMapper,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return ConversationListItemDTO[]
     */
    public function listConversations(User $user): array
    {
        $conversations = $this->messageRepository->findConversations($user);

        return array_map(
            fn (array $entry) => $this->messageMapper->toConversationListItem(
                conversationId: $entry['counterpart']->getId() ?? 0,
                counterpart: $entry['counterpart'],
                lastMessage: $entry['lastMessage']
            ),
            $conversations
        );
    }

    public function getConversation(User $user, int $counterpartId): ConversationDTO
    {
        $counterpart = $this->userRepository->find($counterpartId);
        if (!$counterpart) {
            throw new InvalidArgumentException('Counterpart not found.');
        }

        $messages = $this->messageRepository->findMessagesBetween($user, $counterpart);

        return $this->messageMapper->toConversationDTO(
            conversationId: $counterpartId,
            counterpart: $counterpart,
            currentUser: $user,
            messages: $messages,
        );
    }

    public function sendMessage(User $sender, int $counterpartId, string $content, ?int $jobId = null, ?int $contractId = null): MessageDTO
    {
        $counterpart = $this->userRepository->find($counterpartId);
        if (!$counterpart) {
            throw new InvalidArgumentException('Counterpart not found.');
        }

        $message = new Message();
        $cleanContent = $this->sanitizeContent($content);
        if ($cleanContent === '') {
            throw new InvalidArgumentException('Message cannot be empty.');
        }

        $message->setSender($sender)
            ->setReceiver($counterpart)
            ->setContent($cleanContent);

        if ($jobId) {
            $job = $this->jobRepository->find($jobId);
            if ($job) {
                $message->setJob($job);
            }
        }

        if ($contractId) {
            $contract = $this->contractRepository->find($contractId);
            if ($contract) {
                $message->setContract($contract);
            }
        }

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return new MessageDTO(
            id: $message->getId() ?? 0,
            senderId: $sender->getId() ?? 0,
            content: $message->getContent(),
            sentAt: $message->getSentAt()->format(DATE_ATOM),
            isMine: true,
        );
    }

    private function sanitizeContent(string $content): string
    {
        return trim(strip_tags($content));
    }
}
