<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'freelanceContracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $freelancer = null;

    #[ORM\ManyToOne(inversedBy: 'clientContracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\OneToOne(inversedBy: 'contract', cascade: ['persist'])]
    private ?Job $job = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private string $amountUsdc;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $periodStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $periodEnd = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $scopeExpected = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $scopeDelivered = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\OneToOne(mappedBy: 'contract', cascade: ['persist', 'remove'])]
    private ?Project $project = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(mappedBy: 'contract', targetEntity: Transaction::class)]
    private Collection $transactions;

    /**
     * @var Collection<int, SmartContract>
     */
    #[ORM\OneToMany(mappedBy: 'contract', targetEntity: SmartContract::class)]
    private Collection $smartContracts;

    /**
     * @var Collection<int, DaoDispute>
     */
    #[ORM\OneToMany(mappedBy: 'contract', targetEntity: DaoDispute::class)]
    private Collection $daoDisputes;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->transactions = new ArrayCollection();
        $this->smartContracts = new ArrayCollection();
        $this->daoDisputes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFreelancer(): ?User
    {
        return $this->freelancer;
    }

    public function setFreelancer(User $freelancer): self
    {
        $this->freelancer = $freelancer;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getAmountUsdc(): string
    {
        return $this->amountUsdc;
    }

    public function setAmountUsdc(string $amountUsdc): self
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getScopeExpected(): ?string
    {
        return $this->scopeExpected;
    }

    public function setScopeExpected(?string $scopeExpected): self
    {
        $this->scopeExpected = $scopeExpected;

        return $this;
    }

    public function getScopeDelivered(): ?string
    {
        return $this->scopeDelivered;
    }

    public function setScopeDelivered(?string $scopeDelivered): self
    {
        $this->scopeDelivered = $scopeDelivered;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * @return Collection<int, SmartContract>
     */
    public function getSmartContracts(): Collection
    {
        return $this->smartContracts;
    }

    /**
     * @return Collection<int, DaoDispute>
     */
    public function getDaoDisputes(): Collection
    {
        return $this->daoDisputes;
    }
}
