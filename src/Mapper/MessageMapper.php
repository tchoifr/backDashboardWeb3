<?php

namespace App\Mapper;

use App\DTO\Message\ConversationDTO;
use App\DTO\Message\ConversationListItemDTO;
use App\DTO\Message\MessageDTO;
use App\DTO\Profile\UserProfileDTO;
use App\Entity\Message;
use App\Entity\User;
use DateTimeInterface;

class MessageMapper
{
    public function __construct(private readonly UserMapper $userMapper)
    {
    }

    public function toConversationListItem(int $conversationId, User $counterpart, ?Message $lastMessage): ConversationListItemDTO
    {
        return new ConversationListItemDTO(
            conversationId: $conversationId,
            counterpartName: $counterpart->getUsername() ?? $counterpart->getWalletAddress(),
            lastMessagePreview: $lastMessage?->getContent(),
            lastMessageAt: $lastMessage?->getSentAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param Message[] $messages
     */
    public function toConversationDTO(int $conversationId, User $counterpart, User $currentUser, array $messages): ConversationDTO
    {
        $messageDTOs = array_map(
            fn (Message $message): MessageDTO => new MessageDTO(
                id: $message->getId() ?? 0,
                senderId: $message->getSender()?->getId() ?? 0,
                content: $message->getContent(),
                sentAt: $message->getSentAt()->format(DateTimeInterface::ATOM),
                isMine: $message->getSender()?->getId() === $currentUser->getId(),
            ),
            $messages
        );

        $profile = $this->userMapper->toProfileDTO($counterpart);

        return new ConversationDTO(
            conversationId: $conversationId,
            counterpart: $profile,
            messages: $messageDTOs,
        );
    }
}
