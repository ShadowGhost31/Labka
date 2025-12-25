<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 120)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /** @var Collection<int, UserRole> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserRole::class, orphanRemoval: true)]
    private Collection $userRoles;

    /** @var Collection<int, Project> */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Project::class)]
    private Collection $ownedProjects;

    /** @var Collection<int, ProjectMember> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProjectMember::class, orphanRemoval: true)]
    private Collection $projectMemberships;

    /** @var Collection<int, Task> */
    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Task::class)]
    private Collection $createdTasks;

    /** @var Collection<int, Task> */
    #[ORM\OneToMany(mappedBy: 'assignee', targetEntity: Task::class)]
    private Collection $assignedTasks;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    private Collection $comments;

    /** @var Collection<int, Attachment> */
    #[ORM\OneToMany(mappedBy: 'uploadedBy', targetEntity: Attachment::class)]
    private Collection $attachments;

    /** @var Collection<int, TimeEntry> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TimeEntry::class)]
    private Collection $timeEntries;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->userRoles = new ArrayCollection();
        $this->ownedProjects = new ArrayCollection();
        $this->projectMemberships = new ArrayCollection();
        $this->createdTasks = new ArrayCollection();
        $this->assignedTasks = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->timeEntries = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /** @return Collection<int, UserRole> */
    public function getUserRoles(): Collection { return $this->userRoles; }
}
