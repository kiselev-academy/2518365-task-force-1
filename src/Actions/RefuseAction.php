<?php

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class RefuseAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'refuse';
    }

    public static function getTitle(): string
    {
        return 'Отказаться';
    }

    public function checkRight(Task $task, int $currentUserId): bool
    {
        return
            $task->currentStatus === Task::STATUS_WORK && $currentUserId === $task->getIdExecutor();
    }
}
