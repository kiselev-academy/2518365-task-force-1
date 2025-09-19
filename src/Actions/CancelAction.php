<?php

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class CancelAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'cancel';
    }

    public static function getTitle(): string
    {
        return 'Отменить';
    }

    public function checkRight(Task $task, int $currentUserId): bool
    {
        return
            $task->currentStatus === Task::STATUS_NEW && $currentUserId === $task->getIdCustomer();
    }
}
