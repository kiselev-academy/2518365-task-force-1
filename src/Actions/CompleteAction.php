<?php

declare(strict_types=1);

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class CompleteAction extends AbstractAction
{
    protected string $name = 'Выполнено';
    protected string $internalName = 'Complete';

    protected function isAvailable(Task $task, int $userId): bool
    {
        if ($task->status === Task::STATUS_WORK && $task->customerId === $userId) {
            return true;
        }
        return false;
    }
}
