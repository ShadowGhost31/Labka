<?php

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Comment;
use App\Entity\Label;
use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Entity\Role;
use App\Entity\Task;
use App\Entity\TaskLabel;
use App\Entity\TaskStatus;
use App\Entity\TimeEntry;
use App\Entity\User;
use App\Entity\UserRole;

final class EntityFactory
{
    public function createRole(string $name): Role
    {
        $e = new Role();
        $e->setName($name);
        return $e;
    }

    public function createUser(string $email, string $name): User
    {
        $e = new User();
        $e->setEmail($email);
        $e->setName($name);
        return $e;
    }

    public function createUserRole(User $user, Role $role): UserRole
    {
        $e = new UserRole();
        $e->setUser($user);
        $e->setRole($role);
        return $e;
    }

    public function createProject(string $title, ?string $description, User $owner): Project
    {
        $e = new Project();
        $e->setTitle($title);
        $e->setDescription($description);
        $e->setOwner($owner);
        return $e;
    }

    public function createProjectMember(Project $project, User $user, Role $role): ProjectMember
    {
        $e = new ProjectMember();
        $e->setProject($project);
        $e->setUser($user);
        $e->setRole($role);
        return $e;
    }

    public function createTaskStatus(string $name, int $sortOrder = 0): TaskStatus
    {
        $e = new TaskStatus();
        $e->setName($name);
        $e->setSortOrder($sortOrder);
        return $e;
    }

    public function createTask(
        string $title,
        ?string $description,
        Project $project,
        TaskStatus $status,
        User $creator,
        ?User $assignee = null,
        ?\DateTimeImmutable $dueAt = null
    ): Task {
        $e = new Task();
        $e->setTitle($title);
        $e->setDescription($description);
        $e->setProject($project);
        $e->setStatus($status);
        $e->setCreator($creator);
        $e->setAssignee($assignee);
        $e->setDueAt($dueAt);
        return $e;
    }

    public function createComment(string $content, Task $task, User $author): Comment
    {
        $e = new Comment();
        $e->setContent($content);
        $e->setTask($task);
        $e->setAuthor($author);
        return $e;
    }

    public function createLabel(string $name, ?string $color = null): Label
    {
        $e = new Label();
        $e->setName($name);
        $e->setColor($color);
        return $e;
    }

    public function createTaskLabel(Task $task, Label $label): TaskLabel
    {
        $e = new TaskLabel();
        $e->setTask($task);
        $e->setLabel($label);
        return $e;
    }

    public function createAttachment(string $filename, string $path, Task $task, User $uploadedBy): Attachment
    {
        $e = new Attachment();
        $e->setFilename($filename);
        $e->setPath($path);
        $e->setTask($task);
        $e->setUploadedBy($uploadedBy);
        return $e;
    }

    public function createTimeEntry(
        int $minutes,
        \DateTimeImmutable $workDate,
        Task $task,
        User $user,
        ?string $note = null
    ): TimeEntry {
        $e = new TimeEntry();
        $e->setMinutes($minutes);
        $e->setWorkDate($workDate);
        $e->setTask($task);
        $e->setUser($user);
        $e->setNote($note);
        return $e;
    }
}
