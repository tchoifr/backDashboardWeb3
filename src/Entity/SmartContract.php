<?php

namespace App\Entity;

use App\Repository\SmartContractRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SmartContractRepository::class)]
class SmartContract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'smartContracts')]
    private ?Contract $contract = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    private string $chain;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contractAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $txHash = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $amountUsdc = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getChain(): string
    {
        return $this->chain;
    }

    public function setChain(string $chain): self
    {
        $this->chain = $chain;

        return $this;
    }

    public function getContractAddress(): ?string
    {
        return $this->contractAddress;
    }

    public function setContractAddress(?string $contractAddress): self
    {
        $this->contractAddress = $contractAddress;

        return $this;
    }

    public function getTxHash(): ?string
    {
        return $this->txHash;
    }

    public function setTxHash(?string $txHash): self
    {
        $this->txHash = $txHash;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmountUsdc(): ?string
    {
        return $this->amountUsdc;
    }

    public function setAmountUsdc(?string $amountUsdc): self
    {
        $this->amountUsdc = $amountUsdc;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
