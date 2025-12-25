<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'projects')]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['project:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['project:write']],
            normalizationContext: ['groups' => ['project:read']]
        ),
        new Get(
            normalizationContext: ['groups' => ['project:read']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['project:write']],
            normalizationContext: ['groups' => ['project:read']]
        ),
        new Delete(),

        // Lab 8 Action #1
        new Get(
            uriTemplate: '/projects/{id}/stats',
            requirements: ['id' => '\d+'],
            controller: \App\Controller\Api\ProjectStatsAction::class,
            read: true,          // Doctrine завантажить Project по id
            deserialize: false,  // GET не має body
            output: false,       // JsonResponse повертає контролер
            name: 'project_stats',
            openapi: new Operation(
                summary: 'Project статистика (кількість задач та учасників)',
                description: 'Кастомний Action для Lab 8. Повертає JSON зі статистикою проєкту.',
                responses: [
                    '200' => new Response(description: 'OK'),
                    '404' => new Response(description: 'Project not found'),
                ]
            )
        ),
    ]
)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    #[Groups(['project:read', 'project:write'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['project:read', 'project:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['project:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'ownedProjects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['project:read', 'project:write'])]
    private ?User $owner = null;

    /** @var Collection<int, ProjectMember> */
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ProjectMember::class, orphanRemoval: true)]
    #[Groups(['project:read'])]
    private Collection $members;

    /** @var Collection<int, Task> */
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class, orphanRemoval: true)]
    #[Groups(['project:read'])]
    private Collection $tasks;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->members = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    /** @return Collection<int, ProjectMember> */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /** @return Collection<int, Task> */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addMember(ProjectMember $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->setProject($this);
        }
        return $this;
    }

    public function removeMember(ProjectMember $member): static
    {
        if ($this->members->removeElement($member)) {
            if ($member->getProject() === $this) {
                $member->setProject(null);
            }
        }
        return $this;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProject($this);
        }
        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }
        return $this;
    }
}
