<?php

namespace App\Mapper;

use App\DTO\Dashboard\TransactionItemDTO;
use App\Entity\Transaction;
use DateTimeInterface;

class TransactionMapper
{
    public function toItemDTO(Transaction $transaction): TransactionItemDTO
    {
        return new TransactionItemDTO(
            id: $transaction->getId() ?? 0,
            label: $transaction->getLabel(),
            amountUsdc: $transaction->getAmountUsdc(),
            direction: $transaction->getDirection(),
            date: $transaction->getDate()->format(DateTimeInterface::ATOM),
            status: $transaction->getStatus(),
        );
    }
}
