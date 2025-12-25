<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'roles')]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['role:read']]),
        new Post(denormalizationContext: ['groups' => ['role:write']], normalizationContext: ['groups' => ['role:read']]),
        new Get(normalizationContext: ['groups' => ['role:read']]),
        new Patch(denormalizationContext: ['groups' => ['role:write']], normalizationContext: ['groups' => ['role:read']]),
        new Delete(),
    ]
)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["role:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(["role:read", "role:write"])]
    private ?string $name = null;

    /** @var Collection<int, UserRole> */
    #[ORM\OneToMany(mappedBy: 'role', targetEntity: UserRole::class, orphanRemoval: true)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["role:read", "role:write"])]
    private Collection $userRoles;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /** @return Collection<int, UserRole> */
    public function getUserRoles(): Collection { return $this->userRoles; }

    public function addUserRole(UserRole $userRole): static
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles->add($userRole);
            $userRole->setRole($this);
        }

        return $this;
    }

    public function removeUserRole(UserRole $userRole): static
    {
        if ($this->userRoles->removeElement($userRole)) {
            // set the owning side to null (unless already changed)
            if ($userRole->getRole() === $this) {
                $userRole->setRole(null);
            }
        }

        return $this;
    }
}
