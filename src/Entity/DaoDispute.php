<?php

namespace App\Entity;

use App\Repository\DaoDisputeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DaoDisputeRepository::class)]
class DaoDispute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'disputes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dao $dao = null;

    #[ORM\ManyToOne(inversedBy: 'daoDisputes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contract $contract = null;

    #[ORM\ManyToOne(inversedBy: 'raisedDisputes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $raisedBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionExpected = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionDelivered = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $amountUsdc = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $periodStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $periodEnd = null;

    #[ORM\Column]
    private int $votesFavorable = 0;

    #[ORM\Column]
    private int $votesTotal = 0;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDao(): ?Dao
    {
        return $this->dao;
    }

    public function setDao(Dao $dao): self
    {
        $this->dao = $dao;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getRaisedBy(): ?User
    {
        return $this->raisedBy;
    }

    public function setRaisedBy(User $raisedBy): self
    {
        $this->raisedBy = $raisedBy;

        return $this;
    }

    public function getDescriptionExpected(): ?string
    {
        return $this->descriptionExpected;
    }

    public function setDescriptionExpected(?string $descriptionExpected): self
    {
        $this->descriptionExpected = $descriptionExpected;

        return $this;
    }

    public function getDescriptionDelivered(): ?string
    {
        return $this->descriptionDelivered;
    }

    public function setDescriptionDelivered(?string $descriptionDelivered): self
    {
        $this->descriptionDelivered = $descriptionDelivered;

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

    public function getPeriodStart(): ?\DateTimeInterface
    {
        return $this->periodStart;
    }

    public function setPeriodStart(?\DateTimeInterface $periodStart): self
    {
        $this->periodStart = $periodStart;

        return $this;
    }

    public function getPeriodEnd(): ?\DateTimeInterface
    {
        return $this->periodEnd;
    }

    public function setPeriodEnd(?\DateTimeInterface $periodEnd): self
    {
        $this->periodEnd = $periodEnd;

        return $this;
    }

    public function getVotesFavorable(): int
    {
        return $this->votesFavorable;
    }

    public function setVotesFavorable(int $votesFavorable): self
    {
        $this->votesFavorable = $votesFavorable;

        return $this;
    }

    public function getVotesTotal(): int
    {
        return $this->votesTotal;
    }

    public function setVotesTotal(int $votesTotal): self
    {
        $this->votesTotal = $votesTotal;

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
}
