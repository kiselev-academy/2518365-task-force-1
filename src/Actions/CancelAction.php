<?php

declare(strict_types=1);

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class CancelAction extends AbstractAction
{
    protected string $name = 'Отменить';
    protected string $internalName = 'Cancel';

    protected function isAvailable(Task $task, int $userId): bool
    {
        if ($task->status === Task::STATUS_NEW && $task->customerId === $userId) {
            return true;
        }
        return false;
    }
}
