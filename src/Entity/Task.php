<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dueAt = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TaskStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'createdTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\ManyToOne(inversedBy: 'assignedTasks')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $assignee = null;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /** @var Collection<int, Attachment> */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Attachment::class, orphanRemoval: true)]
    private Collection $attachments;

    /** @var Collection<int, TimeEntry> */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TimeEntry::class, orphanRemoval: true)]
    private Collection $timeEntries;

    /** @var Collection<int, TaskLabel> */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskLabel::class, orphanRemoval: true)]
    private Collection $taskLabels;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->timeEntries = new ArrayCollection();
        $this->taskLabels = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getDueAt(): ?\DateTimeImmutable { return $this->dueAt; }
    public function setDueAt(?\DateTimeImmutable $dueAt): static { $this->dueAt = $dueAt; return $this; }

    public function getProject(): ?Project { return $this->project; }
    public function setProject(Project $project): static { $this->project = $project; return $this; }

    public function getStatus(): ?TaskStatus { return $this->status; }
    public function setStatus(TaskStatus $status): static { $this->status = $status; return $this; }

    public function getCreator(): ?User { return $this->creator; }
    public function setCreator(User $creator): static { $this->creator = $creator; return $this; }

    public function getAssignee(): ?User { return $this->assignee; }
    public function setAssignee(?User $assignee): static { $this->assignee = $assignee; return $this; }
}
