<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['get:collection:test']],
        ),
        new Post(
            denormalizationContext: ['groups' => ['post:collection:test']],
            normalizationContext: ['groups' => ['get:item:test']],
        ),
        new Get(
            normalizationContext: ['groups' => ['get:item:test']],
        ),
        new Patch(
            denormalizationContext: ['groups' => ['patch:item:test']],
            normalizationContext: ['groups' => ['get:item:test']],
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
#[ORM\Table(name: 'tests')]
class Test
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['get:collection:test', 'get:item:test'])]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'get:collection:test',
        'get:item:test',
        'post:collection:test',
        'patch:item:test',
    ])]
    private string $name;

    // Using string here to avoid float precision issues.
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups([
        'get:collection:test',
        'get:item:test',
        'post:collection:test',
        'patch:item:test',
    ])]
    private string $price;

    public function __construct(string $name = '', string $price = '0.00')
    {
        $this->id = Uuid::v4();
        $this->name = $name;
        $this->price = $price;
    }

    public function getId(): Uuid
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

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
