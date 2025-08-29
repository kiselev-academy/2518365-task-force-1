<?php

declare(strict_types=1);

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class RespondAction extends AbstractAction
{
    protected string $name = 'Откликнуться';
    protected string $internalName = 'Respond';

    protected function isAvailable(Task $task, int $userId): bool
    {
        if ($task->status === Task::STATUS_NEW && $task->executorId === $userId) {
            return true;
        }
        return false;
    }
}

