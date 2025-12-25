<?php

namespace App\EventListener;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Psr\Log\LoggerInterface;

/**
 * Lab 8 Event #2 (Doctrine postUpdate)
 */
#[AsDoctrineListener(event: 'postUpdate')]
final class ProjectPostUpdateListener
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Project) {
            return;
        }

        $this->logger->info('Project updated', [
            'projectId' => $entity->getId(),
            'title' => $entity->getTitle(),
        ]);
    }
}
