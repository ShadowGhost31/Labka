<?php

namespace App\EventListener;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Psr\Log\LoggerInterface;

/**
 * Lab 8 Event #1 (Doctrine postUpdate)
 */
#[AsDoctrineListener(event: 'postUpdate')]
final class TaskPostUpdateListener
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Task) {
            return;
        }

        $this->logger->info('Task updated', [
            'taskId' => $entity->getId(),
            'title' => $entity->getTitle(),
        ]);
    }
}
