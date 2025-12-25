<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'time_entries')]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['time_entry:read']]),
        new Post(denormalizationContext: ['groups' => ['time_entry:write']], normalizationContext: ['groups' => ['time_entry:read']]),
        new Get(normalizationContext: ['groups' => ['time_entry:read']]),
        new Patch(denormalizationContext: ['groups' => ['time_entry:write']], normalizationContext: ['groups' => ['time_entry:read']]),
        new Delete(),
    ]
)]
class TimeEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["time_entry:read"])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(["time_entry:read", "time_entry:write"])]
    private int $minutes = 0;

    #[ORM\Column(type: 'date_immutable')]
    #[Groups(["time_entry:read", "time_entry:write"])]
    private ?\DateTimeImmutable $workDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["time_entry:read", "time_entry:write"])]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["time_entry:read", "time_entry:write"])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["time_entry:read", "time_entry:write"])]
    private ?Task $task = null;

    public function __construct()
    {
        $this->workDate = new \DateTimeImmutable('today');
    }

    public function getId(): ?int { return $this->id; }

    public function getMinutes(): int { return $this->minutes; }
    public function setMinutes(int $minutes): static { $this->minutes = $minutes; return $this; }

    public function getWorkDate(): ?\DateTimeImmutable { return $this->workDate; }
    public function setWorkDate(\DateTimeImmutable $workDate): static { $this->workDate = $workDate; return $this; }

    public function getNote(): ?string { return $this->note; }
    public function setNote(?string $note): static { $this->note = $note; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): static { $this->user = $user; return $this; }

    public function getTask(): ?Task { return $this->task; }
    public function setTask(Task $task): static { $this->task = $task; return $this; }
}
