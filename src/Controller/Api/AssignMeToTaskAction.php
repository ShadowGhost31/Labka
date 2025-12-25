<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Lab 8 Action #2
 * POST /api/tasks/{id}/assign-me
 */
#[AsController]
final class AssignMeToTaskAction
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(Task $data): Task
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('You must be authenticated.');
        }

        $data->setAssignee($user);
        $this->em->flush();

        return $data;
    }
}
