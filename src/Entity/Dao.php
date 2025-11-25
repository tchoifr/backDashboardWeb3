<?php

namespace App\Entity;

use App\Repository\DaoRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DaoRepository::class)]
class Dao
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, DaoMember>
     */
    #[ORM\OneToMany(mappedBy: 'dao', targetEntity: DaoMember::class, orphanRemoval: true)]
    private Collection $members;

    /**
     * @var Collection<int, DaoDispute>
     */
    #[ORM\OneToMany(mappedBy: 'dao', targetEntity: DaoDispute::class, orphanRemoval: true)]
    private Collection $disputes;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->members = new ArrayCollection();
        $this->disputes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, DaoMember>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * @return Collection<int, DaoDispute>
     */
    public function getDisputes(): Collection
    {
        return $this->disputes;
    }
}
