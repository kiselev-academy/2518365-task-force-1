<?php

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class RespondAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'respond';
    }

    public static function getTitle(): string
    {
        return 'Откликнуться';
    }

    public function checkRight(Task $task, int $currentUserId): bool
    {
        return
            $task->currentStatus === Task::STATUS_NEW && $currentUserId === $task->getIdExecutor();
    }
}
