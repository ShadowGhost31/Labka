<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'time_entries')]
class TimeEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    
    #[Assert\NotNull]

    #[Assert\Positive]
private int $minutes = 0;

    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $workDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    
    #[Assert\Length(max: 1000)]
private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    
    #[Assert\NotNull]
private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    
    #[Assert\NotNull]
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
