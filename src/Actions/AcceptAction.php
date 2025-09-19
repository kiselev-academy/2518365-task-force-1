<?php

namespace Taskforce\Actions;

use Taskforce\Models\Task;

class AcceptAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'accept';
    }

    public static function getTitle(): string
    {
        return 'Принять';
    }

    public function checkRight(Task $task, int $currentUserId): bool
    {
        return
            $task->currentStatus === Task::STATUS_WORK && $currentUserId === $task->getIdCustomer();
    }
}