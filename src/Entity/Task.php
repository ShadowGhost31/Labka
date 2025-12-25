<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
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

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTask($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTask() === $this) {
                $comment->setTask(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): static
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setTask($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): static
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getTask() === $this) {
                $attachment->setTask(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TimeEntry>
     */
    public function getTimeEntries(): Collection
    {
        return $this->timeEntries;
    }

    public function addTimeEntry(TimeEntry $timeEntry): static
    {
        if (!$this->timeEntries->contains($timeEntry)) {
            $this->timeEntries->add($timeEntry);
            $timeEntry->setTask($this);
        }

        return $this;
    }

    public function removeTimeEntry(TimeEntry $timeEntry): static
    {
        if ($this->timeEntries->removeElement($timeEntry)) {
            // set the owning side to null (unless already changed)
            if ($timeEntry->getTask() === $this) {
                $timeEntry->setTask(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TaskLabel>
     */
    public function getTaskLabels(): Collection
    {
        return $this->taskLabels;
    }

    public function addTaskLabel(TaskLabel $taskLabel): static
    {
        if (!$this->taskLabels->contains($taskLabel)) {
            $this->taskLabels->add($taskLabel);
            $taskLabel->setTask($this);
        }

        return $this;
    }

    public function removeTaskLabel(TaskLabel $taskLabel): static
    {
        if ($this->taskLabels->removeElement($taskLabel)) {
            // set the owning side to null (unless already changed)
            if ($taskLabel->getTask() === $this) {
                $taskLabel->setTask(null);
            }
        }

        return $this;
    }
}
