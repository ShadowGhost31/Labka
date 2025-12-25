<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['user:read']]),
        new Post(denormalizationContext: ['groups' => ['user:write']], normalizationContext: ['groups' => ['user:read']]),
        new Get(normalizationContext: ['groups' => ['user:read']]),
        new Patch(denormalizationContext: ['groups' => ['user:write']], normalizationContext: ['groups' => ['user:read']]),
        new Delete(),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["user:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["user:read", "user:write"])]
    private ?string $email = null;

    #[ORM\Column(length: 120)]
    #[Groups(["user:read", "user:write"])]
    private ?string $name = null;


    #[ORM\Column(length: 255)]
    #[Groups(["user:read", "user:write"])]
    private string $password = '';

    #[ORM\Column]
    #[Groups(["user:read", "user:write"])]
    private ?\DateTimeImmutable $createdAt = null;

    /** @var Collection<int, UserRole> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserRole::class, orphanRemoval: true)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user:read", "user:write"])]
    private Collection $userRoles;

    /** @var Collection<int, Project> */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Project::class)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user:read", "user:write"])]
    private Collection $ownedProjects;

    /** @var Collection<int, ProjectMember> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProjectMember::class, orphanRemoval: true)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user:read", "user:write"])]
    private Collection $projectMemberships;

    /** @var Collection<int, Task> */
    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Task::class)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user:read", "user:write"])]
    private Collection $createdTasks;

    /** @var Collection<int, Task> */
    #[ORM\OneToMany(mappedBy: 'assignee', targetEntity: Task::class)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user:read", "user:write"])]
    private Collection $assignedTasks;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user:read", "user:write"])]
    private Collection $comments;

    /** @var Collection<int, Attachment> */
    #[ORM\OneToMany(mappedBy: 'uploadedBy', targetEntity: Attachment::class)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user:read", "user:write"])]
    private Collection $attachments;

    /** @var Collection<int, TimeEntry> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TimeEntry::class)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user:read", "user:write"])]
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

    public function addUserRole(UserRole $userRole): static
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles->add($userRole);
            $userRole->setUser($this);
        }

        return $this;
    }

    public function removeUserRole(UserRole $userRole): static
    {
        if ($this->userRoles->removeElement($userRole)) {
            // set the owning side to null (unless already changed)
            if ($userRole->getUser() === $this) {
                $userRole->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getOwnedProjects(): Collection
    {
        return $this->ownedProjects;
    }

    public function addOwnedProject(Project $ownedProject): static
    {
        if (!$this->ownedProjects->contains($ownedProject)) {
            $this->ownedProjects->add($ownedProject);
            $ownedProject->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedProject(Project $ownedProject): static
    {
        if ($this->ownedProjects->removeElement($ownedProject)) {
            // set the owning side to null (unless already changed)
            if ($ownedProject->getOwner() === $this) {
                $ownedProject->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProjectMember>
     */
    public function getProjectMemberships(): Collection
    {
        return $this->projectMemberships;
    }

    public function addProjectMembership(ProjectMember $projectMembership): static
    {
        if (!$this->projectMemberships->contains($projectMembership)) {
            $this->projectMemberships->add($projectMembership);
            $projectMembership->setUser($this);
        }

        return $this;
    }

    public function removeProjectMembership(ProjectMember $projectMembership): static
    {
        if ($this->projectMemberships->removeElement($projectMembership)) {
            // set the owning side to null (unless already changed)
            if ($projectMembership->getUser() === $this) {
                $projectMembership->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getCreatedTasks(): Collection
    {
        return $this->createdTasks;
    }

    public function addCreatedTask(Task $createdTask): static
    {
        if (!$this->createdTasks->contains($createdTask)) {
            $this->createdTasks->add($createdTask);
            $createdTask->setCreator($this);
        }

        return $this;
    }

    public function removeCreatedTask(Task $createdTask): static
    {
        if ($this->createdTasks->removeElement($createdTask)) {
            // set the owning side to null (unless already changed)
            if ($createdTask->getCreator() === $this) {
                $createdTask->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getAssignedTasks(): Collection
    {
        return $this->assignedTasks;
    }

    public function addAssignedTask(Task $assignedTask): static
    {
        if (!$this->assignedTasks->contains($assignedTask)) {
            $this->assignedTasks->add($assignedTask);
            $assignedTask->setAssignee($this);
        }

        return $this;
    }

    public function removeAssignedTask(Task $assignedTask): static
    {
        if ($this->assignedTasks->removeElement($assignedTask)) {
            // set the owning side to null (unless already changed)
            if ($assignedTask->getAssignee() === $this) {
                $assignedTask->setAssignee(null);
            }
        }

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
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
            $attachment->setUploadedBy($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): static
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getUploadedBy() === $this) {
                $attachment->setUploadedBy(null);
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
            $timeEntry->setUser($this);
        }

        return $this;
    }

    public function removeTimeEntry(TimeEntry $timeEntry): static
    {
        if ($this->timeEntries->removeElement($timeEntry)) {
            // set the owning side to null (unless already changed)
            if ($timeEntry->getUser() === $this) {
                $timeEntry->setUser(null);
            }
        }

        return $this;
    }
public function getUserIdentifier(): string
{
    return (string) $this->email;
}

public function getPassword(): string
{
    return $this->password;
}

public function setPassword(string $password): static
{
    $this->password = $password;

    return $this;
}

public function getRoles(): array
{
    $roles = ['ROLE_USER'];

    foreach ($this->userRoles as $userRole) {
        $role = $userRole->getRole();
        if ($role && $role->getName()) {
            $roles[] = 'ROLE_' . strtoupper($role->getName());
        }
    }

    return array_values(array_unique($roles));
}

public function eraseCredentials(): void
{
    // no-op
}

}
