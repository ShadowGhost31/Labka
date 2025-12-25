<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'labels')]
class Label
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    
    #[Assert\NotBlank]

    #[Assert\Length(max: 50)]
private ?string $name = null;

    #[ORM\Column(length: 16, nullable: true)]
    
    #[Assert\Regex(pattern: '/^#?[0-9A-Fa-f]{6}$/', message: 'Color must be hex like #AABBCC')]
private ?string $color = null;

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getColor(): ?string { return $this->color; }
    public function setColor(?string $color): static { $this->color = $color; return $this; }
}
