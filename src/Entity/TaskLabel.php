<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'task_labels', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_task_label', columns: ['task_id', 'label_id'])
])]
class TaskLabel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taskLabels')]
    #[ORM\JoinColumn(nullable: false)]
    
    #[Assert\NotNull]
private ?Task $task = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Label $label = null;

    public function getId(): ?int { return $this->id; }

    public function getTask(): ?Task { return $this->task; }
    public function setTask(Task $task): static { $this->task = $task; return $this; }

    public function getLabel(): ?Label { return $this->label; }
    public function setLabel(Label $label): static { $this->label = $label; return $this; }
}
