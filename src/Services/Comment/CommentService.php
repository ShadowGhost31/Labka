<?php

namespace App\Services\Comment;

use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\User;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class CommentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(string $content, Task $task, User $author): Comment
    {
        $c = new Comment();
        $c->setContent($content);
        $c->setTask($task);
        $c->setAuthor($author);
        $c->setCreatedAt(new \DateTimeImmutable());

        $this->requestChecker->validateRequestDataByConstraints($c);
        $this->entityManager->persist($c);
        return $c;
    }

    /** @param array<string,mixed> $data */
    public function update(Comment $c, array $data, ?Task $task = null, ?User $author = null): void
    {
        if (array_key_exists('content', $data)) $c->setContent((string)$data['content']);
        if ($task) $c->setTask($task);
        if ($author) $c->setAuthor($author);

        $this->requestChecker->validateRequestDataByConstraints($c);
    }
}
