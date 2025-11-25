<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $walletAddress;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $availability = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $rateHourUsdc = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $skills = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $highlights = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $portfolio = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $lastLoginAt = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * @var Collection<int, Job>
     */
    #[ORM\OneToMany(mappedBy: 'postedBy', targetEntity: Job::class)]
    private Collection $postedJobs;

    /**
     * @var Collection<int, Job>
     */
    #[ORM\OneToMany(mappedBy: 'assignedTo', targetEntity: Job::class)]
    private Collection $assignedJobs;

    /**
     * @var Collection<int, JobApplication>
     */
    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: JobApplication::class, orphanRemoval: true)]
    private Collection $jobApplications;

    /**
     * @var Collection<int, Contract>
     */
    #[ORM\OneToMany(mappedBy: 'freelancer', targetEntity: Contract::class)]
    private Collection $freelanceContracts;

    /**
     * @var Collection<int, Contract>
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Contract::class)]
    private Collection $clientContracts;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Transaction::class)]
    private Collection $transactions;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    private Collection $sentMessages;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Message::class)]
    private Collection $receivedMessages;

    /**
     * @var Collection<int, DaoMember>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DaoMember::class, orphanRemoval: true)]
    private Collection $daoMemberships;

    /**
     * @var Collection<int, DaoDispute>
     */
    #[ORM\OneToMany(mappedBy: 'raisedBy', targetEntity: DaoDispute::class)]
    private Collection $raisedDisputes;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->roles = ['ROLE_USER'];
        $this->postedJobs = new ArrayCollection();
        $this->assignedJobs = new ArrayCollection();
        $this->jobApplications = new ArrayCollection();
        $this->freelanceContracts = new ArrayCollection();
        $this->clientContracts = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->daoMemberships = new ArrayCollection();
        $this->raisedDisputes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWalletAddress(): string
    {
        return $this->walletAddress;
    }

    public function setWalletAddress(string $walletAddress): self
    {
        $this->walletAddress = strtolower($walletAddress);

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    public function setAvailability(?string $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getRateHourUsdc(): ?string
    {
        return $this->rateHourUsdc;
    }

    public function setRateHourUsdc(?string $rateHourUsdc): self
    {
        $this->rateHourUsdc = $rateHourUsdc;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getSkills(): ?array
    {
        return $this->skills;
    }

    public function setSkills(?array $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function getHighlights(): ?array
    {
        return $this->highlights;
    }

    public function setHighlights(?array $highlights): self
    {
        $this->highlights = $highlights;

        return $this;
    }

    public function getPortfolio(): ?array
    {
        return $this->portfolio;
    }

    public function setPortfolio(?array $portfolio): self
    {
        $this->portfolio = $portfolio;

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

    public function getLastLoginAt(): ?DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getRoles(): array
    {
        return array_values(array_unique($this->roles ?: ['ROLE_USER']));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUserIdentifier(): string
    {
        return $this->walletAddress;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getPostedJobs(): Collection
    {
        return $this->postedJobs;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getAssignedJobs(): Collection
    {
        return $this->assignedJobs;
    }

    /**
     * @return Collection<int, JobApplication>
     */
    public function getJobApplications(): Collection
    {
        return $this->jobApplications;
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getFreelanceContracts(): Collection
    {
        return $this->freelanceContracts;
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getClientContracts(): Collection
    {
        return $this->clientContracts;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    /**
     * @return Collection<int, DaoMember>
     */
    public function getDaoMemberships(): Collection
    {
        return $this->daoMemberships;
    }

    /**
     * @return Collection<int, DaoDispute>
     */
    public function getRaisedDisputes(): Collection
    {
        return $this->raisedDisputes;
    }
}
