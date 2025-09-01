<?php

declare(strict_types=1);

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class RefuseAction extends AbstractAction
{
    protected string $name = 'Отказаться';
    protected string $internalName = 'Refuse';

    protected function isAvailable(Task $task, int $userId): bool
    {
        if ($task->status === Task::STATUS_WORK && $task->executorId === $userId) {
            return true;
        }
        return false;
    }
}
